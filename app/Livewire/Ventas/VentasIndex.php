<?php

namespace App\Livewire\Ventas;

use App\Models\Caja;
use Livewire\Component;
use App\Models\Factura;
use App\Models\DetalleVenta;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Services\CajaService;
use App\Services\PagoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class VentasIndex extends Component
{
    use WithPagination;

    public $facturaSeleccionada = null;
    public $mostrarDetalle = false;
    public $mostrarPago = false;
    public $fechaDesde;
    public $fechaHasta;
    public $nombreCliente;
    
    // Variables para el pago
    public $montoPago;
    public $metodoPago = 1;
    public $observacionesPago;
    public $referenciaPago;

    public $entidadBancaria = '';
    public $numeroCheque = '';
    public $fechaVencimientoCheque = '';
    public $entidadBancariaCheque = '';
    public $codigoAutorizacion = '';
    public $referenciaMercadoPago = '';
    public $datosTarjeta = [
        'numero' => '',
        'cuotas' => 1,
        'tipo' => ''
    ];

    public function updatingFechaDesde() { $this->resetPage(); }
    public function updatingFechaHasta() { $this->resetPage(); }
    public function updatingnombreCliente() { $this->resetPage(); }

    protected $rules = [
        'montoPago' => 'required|numeric|min:0.01',
        'metodoPago' => 'required',
        'observacionesPago' => 'nullable|string|max:500',
        'referenciaPago' => 'nullable|string|max:100'
    ];

    public function verDetalle($facturaId)
    {
        $this->facturaSeleccionada = Factura::with('detalles.articulo')->find($facturaId);
        $this->mostrarDetalle = true;
    }

    public function cerrarDetalle()
    {
        $this->mostrarDetalle = false;
        $this->facturaSeleccionada = null;
    }

    public function ingresarPago($facturaId)
    {
        $this->facturaSeleccionada = Factura::with(['cliente', 'pagos'])->find($facturaId);
        $this->montoPago = $this->facturaSeleccionada->saldo_pendiente;
        $this->mostrarPago = true;
    }

    public function confirmarVentaConPago()
    {
        if (!Caja::where('estado', 'abierta')
                        ->exists()) {
            
            $this->addError('Caja', 'Debe abrir la caja antes de iniciar una operacion');
            $this->mostrarPago = false;
            $this->facturaSeleccionada = null;
            return;
        }
        // Validar datos del pago
        $this->validate([
            'montoPago' => 'required|numeric|min:0.01|max:' . $this->facturaSeleccionada->saldo_pendiente,
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

   
    private function agregarDatosEspecificosPago($datosAdicionales)
    {
        switch($this->metodoPago) {
            case 4: // Transferencia
                $datosAdicionales['entidad_bancaria'] = $this->entidadBancaria;
                break;
                
            case 6: // Cheque
                $datosAdicionales['numero_cheque'] = $this->numeroCheque;
                $datosAdicionales['fecha_vencimiento_cheque'] = $this->fechaVencimientoCheque;
                $datosAdicionales['entidad_bancaria'] = $this->entidadBancariaCheque;
                break;
                
            case 2: // Tarjeta Crédito
            case 3: // Tarjeta Débito
                if (!empty($this->datosTarjeta)) {
                    $datosAdicionales['datos_tarjeta'] = $this->datosTarjeta;
                }
                break;
        }
        
        return $datosAdicionales;
    }

    private function validarDatosPorMetodoPago()
    {
        switch($this->metodoPago) {
            case 4: // Transferencia
                $this->validate([
                    'entidadBancaria' => 'required|string|min:3|max:100',
                ]);
                break;
                
            case 6: // Cheque
                $this->validate([
                    'numeroCheque' => 'required|string|min:3|max:50',
                    'fechaVencimientoCheque' => 'required|date|after_or_equal:today',
                    'entidadBancariaCheque' => 'required|string|min:3|max:100',
                ]);
                break;
                
            case 2: // Tarjeta Crédito
            case 3: // Tarjeta Débito
                // Validar datos de tarjeta si los estás capturando
                if (!empty($this->datosTarjeta)) {
                    $this->validate([
                        'datosTarjeta.numero' => 'sometimes|string|min:15|max:16',
                        'datosTarjeta.cuotas' => 'sometimes|integer|min:1|max:36',
                    ]);
                }
                break;
        }
    }

    public function cerrarModalPago()
    {
        $this->mostrarPago = false;
        $this->facturaSeleccionada = null;
        $this->reset(['montoPago', 'metodoPago', 'observacionesPago', 'referenciaPago']);
    }

    public function render()
    {
        $query = Factura::with(['cliente', 'pagos']);


        // Filtrar por fecha - FORMA CORRECTA
        if ($this->fechaDesde || $this->fechaHasta) {
            if ($this->fechaDesde) {
                $query->whereDate('fecha', '>=', $this->fechaDesde);
            }
            if ($this->fechaHasta) {
                $query->whereDate('fecha', '<=', $this->fechaHasta);
            }
        }

        // Filtro por dni o cuit o nombre del cliente
        if ($this->nombreCliente) {
            $query->whereHas('cliente', function ($q) {
                $q->where('nombre', 'like', '%' . $this->nombreCliente . '%')
                ->orWhere('dni', 'like', '%' . $this->nombreCliente . '%')
                ->orWhere('cuit', 'like', '%' . $this->nombreCliente . '%');
            });
        }

        $ventas = $query->orderByDesc('id')->paginate(10);
        
        return view('livewire.ventas.ventas-index', [
            'ventas' => $ventas,
        ]);
    }
}