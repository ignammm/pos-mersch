<?php

namespace App\Livewire\CuentasCorrientes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cliente;
use App\Models\CuentaCorriente;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MetodoPago;
use App\Models\Caja;
use App\Models\Factura;
use App\Services\PagoService;

class CuentaCorrientesIndex extends Component
{
    use WithPagination;

   
    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $selectedCliente = null;
    public $showModal = false;
    public $clienteDetalle = null;
    
    // Variables para el pago real
    public $montoPago = 0;
    public $metodoPago = 1; // Efectivo por defecto
    public $referenciaPago = '';
    public $observacionesPago = '';
    public $entidadBancaria = '';
    public $codigoAutorizacion = '';
    
    // Nuevas variables para selección de facturas
    public $facturasPendientes = [];
    public $facturasSeleccionadas = [];
    public $distribucionPagos = [];

    //Modal de pago
    public $mostrarModalPago = false;
    public $facturaSeleccionada = null;
    public $numeroCheque = '';
    public $fechaVencimientoCheque = '';
    public $entidadBancariaCheque = '';
    public $referenciaMercadoPago = '';
    public $datosTarjeta = [
        'numero' => '',
        'cuotas' => 1,
        'tipo' => ''
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedMontoPago($value)
    {
        if ($this->clienteDetalle) {
            $this->calcularDistribucionAutomatica();
        }
    }

    public function updatedMetodoPago($value)
    {
        $this->metodoPago = $value;
        if ($this->clienteDetalle) {
            $this->reset(['referenciaPago', 'entidadBancaria', 'codigoAutorizacion']);
            $this->calcularDistribucionAutomatica();
        }
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->where('permite_cuenta_corriente', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('cuit', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        // Calcular saldos actuales para cada cliente
        $clientes->getCollection()->transform(function ($cliente) {
            $cliente->saldo_actual = $this->calcularSaldoActual($cliente->id);
            $cliente->movimientos_pendientes = $this->contarMovimientosPendientes($cliente->id);
            $cliente->total_adeudado = $this->calcularTotalAdeudado($cliente->id);
            return $cliente;
        });

        $metodosPago = MetodoPago::where('activo', true)->get();

        return view('livewire.cuentas-corrientes.cuenta-corrientes-index', [
            'clientes' => $clientes,
            'metodosPago' => $metodosPago,
        ]);
    }

    public function verDetalles($clienteId)
    {
        $this->clienteDetalle = Cliente::with(['cuentasCorrientes.factura', 'cuentasCorrientes.pago'])
            ->findOrFail($clienteId);

        $this->clienteDetalle->saldo_actual = $this->calcularSaldoActual($clienteId);
        $this->clienteDetalle->movimientos_pendientes = $this->contarMovimientosPendientes($clienteId);
        $this->clienteDetalle->total_adeudado = $this->calcularTotalAdeudado($clienteId);

        if ($this->clienteDetalle->total_adeudado > 0) {
            $this->mostrarModalPago = true;
        }
        // Cargar facturas pendientes del cliente
        $this->cargarFacturasPendientes($clienteId);
        
        $this->montoPago = max(0, $this->clienteDetalle->saldo_actual);
        $this->showModal = true;
    }

    private function cargarFacturasPendientes($clienteId)
    {
        $this->facturasPendientes = Factura::where('cliente_id', $clienteId)
            ->where('saldo_pendiente', '>', 0)
            ->where('estado', '!=', 'pagada')
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(function ($factura) {
                return [
                    'id' => $factura->id,
                    'numero' => $factura->numero,
                    'fecha' => $factura->fecha,
                    'total' => $factura->monto_final,
                    'saldo_pendiente' => $factura->saldo_pendiente,
                    'seleccionada' => false,
                    'monto_aplicar' => 0
                ];
            })->toArray();

        $this->facturasSeleccionadas = [];
        $this->distribucionPagos = [];
    }

    public function updatedFacturasSeleccionadas()
    {
        $this->calcularDistribucionAutomatica();
    }

    private function distribuirMontoRestante($montoRestante)
    {
        $facturasSeleccionadas = array_filter($this->facturasPendientes, function($factura) {
            return in_array($factura['id'], $this->facturasSeleccionadas);
        });

        foreach ($facturasSeleccionadas as &$factura) {
            $saldoRestanteFactura = $factura['saldo_pendiente'] - $factura['monto_aplicar'];
            if ($saldoRestanteFactura > 0 && $montoRestante > 0) {
                $montoAdicional = min($saldoRestanteFactura, $montoRestante);
                $factura['monto_aplicar'] += $montoAdicional;
                $this->distribucionPagos[$factura['id']] = $factura['monto_aplicar'];
                $montoRestante -= $montoAdicional;
            }
        }
    }

    public function registrarPago()
    {
        $this->validate([
            'montoPago' => 'required|numeric|min:0.01|max:' . $this->clienteDetalle->saldo_actual,
            'metodoPago' => 'required|exists:metodos_pago,id',
            'referenciaPago' => 'nullable|string|max:100',
            'observacionesPago' => 'nullable|string|max:500',
        ]);

        // Validar que se hayan seleccionado facturas
        if (empty($this->facturasSeleccionadas)) {
            $this->dispatch('error', message: 'Debe seleccionar al menos una factura para aplicar el pago.');
            return;
        }

        // Validar que la suma de la distribución sea igual al monto del pago
        $sumaDistribucion = array_sum($this->distribucionPagos);
        if (abs($sumaDistribucion - $this->montoPago) > 0.01) {
            $this->dispatch('error', message: 'La distribución del pago no coincide con el monto total.');
            return;
        }

        // Validar caja abierta para métodos que la requieren
        if ($this->requiereCaja && !$this->hayCajaAbierta) { // ← Cambiar aquí
        $this->dispatch('error', 
            message: 'No hay una caja abierta para registrar este tipo de pago. Por favor, abra una caja primero.'
        );
        return;
    }
        DB::beginTransaction();

        try {
            $metodoPago = MetodoPago::find($this->metodoPago);
            $comision = $metodoPago->calcularComision($this->montoPago);
            $neto = $this->montoPago - $comision;

            // Obtener caja si el método la requiere
            $cajaId = null;
            if ($this->requiereCaja()) {
                $caja = Caja::where('user_id', Auth::id())
                            ->where('estado', 'abierta')
                            ->first();
                $cajaId = $caja->id;
            }

            // 1. CREAR UN PAGO POR CADA FACTURA
            foreach ($this->distribucionPagos as $facturaId => $montoFactura) {
                if ($montoFactura > 0) {
                    $factura = Factura::find($facturaId);
                    
                    // Crear pago para esta factura
                    $pago = Pago::create([
                        'factura_id' => $facturaId,
                        'metodo_pago_id' => $this->metodoPago,
                        'caja_id' => $cajaId,
                        'monto' => $montoFactura,
                        'comision' => $comision * ($montoFactura / $this->montoPago), // Comisión proporcional
                        'neto' => $neto * ($montoFactura / $this->montoPago), // Neto proporcional
                        'estado' => 'completado',
                        'fecha_pago' => now(),
                        'fecha_confirmacion' => now(),
                        'referencia' => $this->referenciaPago,
                        'codigo_autorizacion' => $this->codigoAutorizacion ?: $this->generarCodigoAutorizacion(),
                        'observaciones' => $this->observacionesPago ?: 
                                          'Pago parcial factura #' . $factura->numero . ' - Cliente: ' . $this->clienteDetalle->nombre,
                        'user_id' => Auth::user()->id,
                        'entidad_bancaria' => $this->entidadBancaria,
                    ]);

                    // 2. Actualizar saldo de la factura
                    $nuevoSaldo = max(0, $factura->saldo_pendiente - $montoFactura);
                    $estadoPago = $nuevoSaldo == 0 ? 'pagada' : ($nuevoSaldo < $factura->monto_final ? 'parcial' : 'pendiente');
                    
                    $factura->update([
                        'saldo_pendiente' => $nuevoSaldo,
                        'estado' => $estadoPago
                    ]);

                    // 3. Registrar en cuenta corriente como HABER (pago recibido)
                    CuentaCorriente::create([
                        'cliente_id' => $this->clienteDetalle->id,
                        'venta_id' => $facturaId,
                        'pago_id' => $pago->id,
                        'tipo_movimiento' => 'haber',
                        'monto' => $montoFactura,
                        'saldo_actual' => $this->calcularSaldoActual($this->clienteDetalle->id) - $montoFactura,
                        'descripcion' => 'Pago factura #' . $factura->numero . ' - ' . $metodoPago->nombre . 
                                       ($this->referenciaPago ? ' - Ref: ' . $this->referenciaPago : ''),
                        'fecha_movimiento' => now(),
                        'estado' => 'pagado',
                    ]);

                    // 4. Registrar en caja si aplica
                    if ($cajaId) {
                        \App\Models\MovimientoCaja::create([
                            'caja_id' => $cajaId,
                            'pago_id' => $pago->id,
                            'user_id' => Auth::id(),
                            'tipo' => 'ingreso',
                            'monto' => $montoFactura,
                            'descripcion' => 'Pago factura #' . $factura->numero . ' - ' . $this->clienteDetalle->nombre,
                            'metodo_pago_id' => $this->metodoPago,
                            'fecha_movimiento' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            $this->showModal = false;
            $this->reset(['montoPago', 'metodoPago', 'referenciaPago', 'observacionesPago', 'entidadBancaria', 'codigoAutorizacion']);
            $this->cargarFacturasPendientes($this->clienteDetalle->id);
            
            $this->dispatch('success', 
                message: 'Pago registrado correctamente en ' . count($this->distribucionPagos) . 
                        ' factura(s) - Total: $' . number_format($this->montoPago, 2)
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->reset(['clienteDetalle', 'montoPago', 'metodoPago', 'referenciaPago', 'observacionesPago', 'entidadBancaria', 'codigoAutorizacion']);
    }

    private function calcularSaldoActual($clienteId)
    {
        $ultimoMovimiento = CuentaCorriente::where('cliente_id', $clienteId)
            ->latest('fecha_movimiento')
            ->first();

        return $ultimoMovimiento ? $ultimoMovimiento->saldo_actual : 0;
    }

    private function contarMovimientosPendientes($clienteId)
    {
        return CuentaCorriente::where('cliente_id', $clienteId)
            ->where('estado', 'pendiente')
            ->where('tipo_movimiento', 'debe')
            ->count();
    }

    private function calcularTotalAdeudado($clienteId)
    {
        return CuentaCorriente::where('cliente_id', $clienteId)
            ->where('estado', 'pendiente')
            ->where('tipo_movimiento', 'debe')
            ->sum('monto');
    }

    private function actualizarEstadosMovimientos($clienteId)
    {
        $saldoActual = $this->calcularSaldoActual($clienteId);
        
        if ($saldoActual <= 0) {
            // Si no hay saldo, marcar todos como pagados
            CuentaCorriente::where('cliente_id', $clienteId)
                ->where('estado', 'pendiente')
                ->update(['estado' => 'pagado']);
        }
    }

    private function requiereCaja()
    {
        return in_array($this->metodoPago, [1, 2, 3]); // Efectivo, tarjetas
    }

    private function hayCajaAbierta()
    {
        return Caja::where('user_id', Auth::id())
                  ->where('estado', 'abierta')
                  ->exists();
    }

    private function generarCodigoAutorizacion()
    {
        return 'AUTH-' . date('YmdHis') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public function exportarReporte()
    {
        $clientes = Cliente::where('permite_cuenta_corriente', true)->get();
        
        $data = [];
        foreach ($clientes as $cliente) {
            $saldo = $this->calcularSaldoActual($cliente->id);
            $data[] = [
                'Cliente' => $cliente->nombre,
                'CUIT/DNI' => $cliente->cuit ?? $cliente->dni,
                'Límite Crédito' => $cliente->limite_credito,
                'Saldo Actual' => $saldo,
                'Disponible' => max(0, $cliente->limite_credito - $saldo),
                'Estado' => $saldo > 0 ? 'Deudor' : 'Al día',
            ];
        }

        $this->dispatch('reporte-generado', data: $data);
    }

    public function getRequiereCajaProperty()
    {
        return in_array($this->metodoPago, [1, 2, 3]); // Efectivo, tarjetas
    }

    public function getHayCajaAbiertaProperty()
    {
        return Caja::where('user_id', Auth::id())
                ->where('estado', 'abierta')
                ->exists();
    }

    public function getTotalDistribuidoProperty()
    {
        return array_sum($this->distribucionPagos);
    }

    public function getHayFacturasSeleccionadasProperty()
    {
        return !empty($this->facturasSeleccionadas);
    }

    public function calcularDistribucionAutomatica()
    {
        if (!$this->clienteDetalle) return;
        
        // Tu lógica de distribución aquí
        $montoDisponible = $this->montoPago;
        $this->distribucionPagos = [];
        
        foreach ($this->facturasPendientes as &$factura) {
            if (in_array($factura['id'], $this->facturasSeleccionadas)) {
                $montoAAplicar = min($factura['saldo_pendiente'], $montoDisponible);
                $factura['monto_aplicar'] = $montoAAplicar;
                $this->distribucionPagos[$factura['id']] = $montoAAplicar;
                $montoDisponible -= $montoAAplicar;
                
                if ($montoDisponible <= 0) break;
            } else {
                $factura['monto_aplicar'] = 0;
            }
        }
        
        if ($montoDisponible > 0) {
            $this->distribuirMontoRestante($montoDisponible);
        }
    }

    public function pagarSaldo()
    {
        
    }

    public function cerrarModalPago()
    {
        $this->mostrarModalPago = false;
        $this->facturaSeleccionada = null;
        $this->reset(['montoPago', 'metodoPago', 'observacionesPago', 'referenciaPago']);
    }

    public function confirmarPago()
    {
        if (!Caja::where('estado', 'abierta')
                        ->exists()) {
            
            $this->addError('Caja', 'Debe abrir la caja antes de iniciar una operacion');
            $this->mostrarModalPago = false;
            return;
        }
        // Validar datos del pago
        $this->validate([
            'montoPago' => 'required|numeric|min:0.01|max:' . $this->clienteDetalle->total_adeudado,
            'metodoPago' => 'required|exists:metodos_pago,id',
        ]);

        // Validaciones específicas por método de pago
        $this->validarDatosPorMetodoPago();

        DB::beginTransaction();

        try {

            $user = Auth::user();

            // 3. PROCESAR EL PAGO CON EL SERVICIO
            $pagoService = new PagoService();
            
            $datosAdicionales = [
                'referencia' => $this->referenciaPago,
                'observaciones' => $this->observacionesPago,
            ];

            // Agregar datos específicos por método de pago
            $datosAdicionales = $this->agregarDatosEspecificosPago($datosAdicionales);

            $pago = $pagoService->procesarPago(
                $this->facturaSeleccionada->id, 
                $this->metodoPago,
                $this->montoPago,
                $datosAdicionales
            );

            DB::commit();

            // 4. LIMPIAR Y MOSTRAR ÉXITO
            $this->cerrarModalPago();
            $this->dispatch('venta-create');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Error al procesar la venta: ' . $e->getMessage());
            dd($e->getMessage());
        }
    }
}