<?php

namespace App\Livewire\Ventas;

use App\Models\Articulo;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Factura;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Stock;
use App\Services\CajaService;
use App\Services\PagoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VentaCreate extends Component
{
    public $cliente_id = 13, $forma_pago, $tipo_comprobante = 'Ticket', $descuento_factura = 0;
    public $codigo_barra, $cantidad = 1, $descuento = 0;
    public $items = [];
    public $total = 0;
    public $modalSeleccionarArticulo = false;
    public $articulosModal;
    public $stockArticulos = [];
    public $mostrarPago = false, $facturaSeleccionada, $montoPago, $metodoPago, $referenciaPago, $observacionesPago;
    public $ventaTemporal = [];
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


    public function agregarArticulo()
    {
        $this->validate([
            'codigo_barra' => 'required',
            'cantidad' => 'required|numeric|min:1',
            'descuento' => 'numeric|min:0',
        ]);

        $existe = Articulo::where('codigo_proveedor', $this->codigo_barra)
        ->orWhere('codigo_fabricante', $this->codigo_barra)
        ->orWhere('articulo', $this->codigo_barra)
        ->exists();
        
        if ($existe) {
            
            if (Articulo::where('codigo_proveedor', $this->codigo_barra)
                ->orWhere('codigo_fabricante', $this->codigo_barra)
                ->orWhere('articulo', $this->codigo_barra)
                ->count() <= 1)
            {
            
                $articulo = Articulo::where('codigo_proveedor', $this->codigo_barra)
                    ->orWhere('codigo_fabricante', $this->codigo_barra)
                    ->orWhere('articulo', $this->codigo_barra)
                    ->first();
                
                if ($this->stockSuperado($articulo)) {
                    return;
                }
                
                if ($this->verificarExisteEnLista($articulo->first())) {
                    return;
                }
                
                $this->agregarArticuloLista($articulo);
                
                $this->calcularTotal();
                $this->reset(['codigo_barra', 'cantidad', 'descuento']);
                
                
            }
            else
            {
                
                $this->articulosModal = Articulo::where('articulo', $this->codigo_barra)->get();
                $this->modalSeleccionarArticulo = true;
                return;
                
            }
             
        }
        else {
            
            $this->addError('codigo_barra', 'El código ingresado no existe.');
            $this->reset(['codigo_barra']);
            return;
        }
        
       
        
    }

    public function incrementarCantidad($index)
    {
        if ($this->stockArticulos[$this->items[$index]['articulo_id']] <= 0) {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock. El stock disponible es de: '. Articulo::find($this->items[$index]['articulo_id'])->stock->cantidad);
            return;
        }

        if (isset($this->items[$index])) {
            $this->items[$index]['cantidad']++;
            $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
            $this->stockArticulos[$this->items[$index]['articulo_id']]--;
            $this->calcularTotal(); // si ya tenés un método de total
        }


    }

    public function decrementarCantidad($index)
    {
        if (isset($this->items[$index]) && $this->items[$index]['cantidad'] > 1) {
            $this->items[$index]['cantidad']--;
            $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
            $this->stockArticulos[$this->items[$index]['articulo_id']]++;
            $this->calcularTotal();
            return;
        }
        $this->addError('cantidad', 'No es posible tener una cantidad menor a 1.');


    }

    public function stockSuperado($articulo)
    {
        if (isset($this->stockArticulos[$articulo->id])) {

            if ($this->stockArticulos[$articulo->id] < $this->cantidad) {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock. El stock disponible es de: '. $this->stockDisponible($articulo));
            $this->reset(['cantidad']);
            $this->modalSeleccionarArticulo = false;
            return true;
            }
            return false;

        }
        if (Articulo::find($articulo->id)->stock->cantidad < $this->cantidad) {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock. El stock disponible es de: '. $this->stockDisponible($articulo));
            $this->reset(['cantidad']);
            $this->modalSeleccionarArticulo = false;
            return true;
            
        }
        return false;
    }

    public function stockDisponible($articulo)
    {
        if (isset($this->items[$articulo->id])) {
           return $this->stockArticulos[$articulo->id];
        }
        $articulo = Articulo::find($articulo->id);
        return $articulo->stock->cantidad;
    }

    public function verificarExisteEnLista($articulo)
    {
        $index = collect($this->items)->search(fn($item) => $item['articulo_id'] === $articulo->id);

        if ($index === false) {
            return false;
        }

        $this->items[$index]['cantidad'] += $this->cantidad;
        $this->items[$index]['subtotal'] = $this->calcularSubtotal(
            $this->items[$index]['precio_unitario'],
            $this->items[$index]['cantidad']
        );

        $this->stockArticulos[$this->items[$index]['articulo_id']] -= $this->cantidad;
        $this->calcularTotal();
        $this->reset(['codigo_barra']);

        return true;
    }

    public function agregarArticuloLista($articulo)
    {
        if ($this->stockSuperado($articulo)) {
            return;
        }

        if (!$this->verificarExisteEnLista($articulo)) {
            
            $this->items[] = [
                'articulo_id' => $articulo->id,
                'nombre' => $articulo->articulo,
                'rubro' => $articulo->rubro,
                'marca' => $articulo->marca,
                'codigo_proveedor' => $articulo->codigo_proveedor,
                'codigo_fabricante' => $articulo->codigo_fabricante,
                'cantidad' => $this->cantidad,
                'precio_unitario' => $this->descuentoUnitario($articulo->precio, $this->descuento),
                'descuento_unitario' => $this->descuento,
                'subtotal' => $this->calcularSubtotal($this->descuentoUnitario($articulo->precio, $this->descuento), $this->cantidad),
            ];

            $this->stockArticulos[$articulo->id] = Articulo::find($articulo->id)->stock->cantidad - $this->cantidad;
        }

        $this->calcularTotal();
        $this->reset(['codigo_barra', 'cantidad', 'descuento']);
        $this->modalSeleccionarArticulo = false;
    }

    public function calcularSubtotal($precio, $cantidad)
    {
        return ($precio * $cantidad);
    }

    public function descuentoUnitario($precio, $descuento)
    {
        // Convertimos a float por seguridad
        $precio = floatval($precio);
        $descuento = floatval($descuento);

        // Validamos que el descuento esté entre 0 y 100
        $descuento = max(0, min($descuento, 100));

        // Calculamos el precio con descuento
        $precioFinal = $precio * (1 - ($descuento / 100));

        return round($precioFinal, 2); // Retornamos con 2 decimales
    }


    public function calcularTotal()
    {
        $this->total = array_sum(array_column($this->items, 'subtotal'));
    }

    public function eliminarItem($index)
    {
        if (isset($this->items[$index])) {
            $articuloId = $this->items[$index]['articulo_id'];

            // devolver al stock del artículo correcto
            if (isset($this->stockArticulos[$articuloId])) {
                $this->stockArticulos[$articuloId] += $this->items[$index]['cantidad'];
            }

            unset($this->items[$index]);
            $this->items = array_values($this->items); // reindexar
            $this->calcularTotal();
        }
    }


    public function confirmarSeleccion($articulo_id)
    {
        $articulo = Articulo::find($articulo_id);
        if ($this->stockSuperado($articulo)) {
            return;
        }
        if ($this->verificarExisteEnLista($articulo)) {
            return;
        }

        $this->agregarArticuloLista($articulo);
    }

    public function confirmarVentaConPago()
    {
        if (!Caja::where('user_id', Auth::id())
                        ->where('estado', 'abierta')
                        ->exists()) {
            
            $this->addError('Caja', 'Debe abrir la caja antes de iniciar una operacion');
            $this->mostrarPago = false;
            $this->facturaSeleccionada = null;
            return;
        }
        // Validar datos del pago
        $this->validate([
            'montoPago' => 'required|numeric|min:0.01|max:' . $this->ventaTemporal['monto_original'],
            'metodoPago' => 'required|exists:metodos_pago,id',
        ]);

        // Validaciones específicas por método de pago
        $this->validarDatosPorMetodoPago();

        DB::beginTransaction();

        try {
            $user = Auth::user();
            
            // 1. CREAR LA FACTURA REAL EN LA BASE DE DATOS
            $venta = Factura::create([
                'cliente_id' => $this->ventaTemporal['cliente_id'],
                'user_id' => $user->id,
                'tipo_comprobante' => $this->ventaTemporal['tipo_comprobante'],
                'numero' => Factura::numeroComprobante($this->ventaTemporal['tipo_comprobante']),
                'monto_original' => $this->ventaTemporal['monto_original'],
                'descuento_aplicado' => $this->ventaTemporal['descuento_aplicado'],
                'fecha' => now(),
                'saldo_pendiente' => $this->ventaTemporal['monto_original'],
                'estado' => 'pendiente',
                'forma_pago' => $this->obtenerNombreMetodoPago($this->metodoPago),
            ]);

            // 2. PROCESAR ITEMS Y STOCK
            foreach ($this->ventaTemporal['items'] as $item) {
                $detalle = DetalleVenta::create([
                    'factura_id' => $venta->id,
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento_aplicado' => $item['descuento_unitario'] ?? 0,
                    'subtotal' => $item['subtotal'],
                ]);

                // Registrar movimiento de inventario
                $detalle->movimientos()->create([
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                    'tipo' => 'salida',
                    'motivo' => 'venta',
                    'estado_reposicion' => 'pendiente',
                ]);

                // Descontar stock
                $stock = Stock::where('articulo_id', $item['articulo_id'])->first();
                if ($stock) {
                    $stock->cantidad = max(0, $stock->cantidad - $item['cantidad']);
                    $stock->save();
                }
            }

            // 3. PROCESAR EL PAGO CON EL SERVICIO
            $pagoService = new PagoService();
            
            $datosAdicionales = [
                'referencia' => $this->referenciaPago,
                'observaciones' => $this->observacionesPago,
            ];

            // Agregar datos específicos por método de pago
            $datosAdicionales = $this->agregarDatosEspecificosPago($datosAdicionales);

            $pago = $pagoService->procesarPago(
                $venta->id, // ID de la factura recién creada
                $this->metodoPago,
                $this->montoPago,
                $datosAdicionales
            );

            DB::commit();

            // 4. LIMPIAR Y MOSTRAR ÉXITO
            $this->cerrarModalPago();
            $this->resetVenta();
            $this->dispatch('venta-create');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Error al procesar la venta: ' . $e->getMessage());
            dd($e->getMessage());
        }
    }

    private function resetVenta()
    {
        $this->reset([
            'cliente_id', 
            'items', 
            'codigo_barra', 
            'cantidad', 
            'descuento', 
            'total', 
            'descuento_factura'
        ]);
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

    private function obtenerNombreMetodoPago($metodoId)
    {
        $metodos = [
            1 => 'efectivo',
            2 => 'tarjeta_credito', 
            3 => 'tarjeta_debito',
            4 => 'transferencia',
            5 => 'cuenta_corriente',
            6 => 'cheque',
            7 => 'otros'
        ];
        
        return $metodos[$metodoId] ?? 'efectivo';
    }

    private function abrirModalPago($factura)
    {
        $this->mostrarPago = true;
        $this->facturaSeleccionada = $factura;

    }

    public function cerrarModalPago()
    {
        $this->mostrarPago = false;
    }

    public function finalizarVenta()
    {
        // Validar que tengamos los datos mínimos
        $this->validate([
            'items' => 'required|array|min:1',
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        
        try {
            // Crear factura temporal en memoria (NO en la base de datos aún)
            $this->ventaTemporal = [
                'cliente_id' => $this->cliente_id,
                'cliente' => Cliente::find($this->cliente_id),
                'tipo_comprobante' => $this->tipo_comprobante,
                'monto_original' => $this->total,
                'saldo_pendiente' => $this->total,
                'descuento_aplicado' => $this->descuento_factura,
                'items' => $this->items,
            ];
            
            // Preparar datos para el modal
            $this->montoPago = $this->total;
            $this->metodoPago = 1; // Efectivo por defecto
            
            // Resetear campos adicionales del pago
            $this->reset(['referenciaPago', 'observacionesPago', 'entidadBancaria', 'numeroCheque', 'fechaVencimientoCheque', 'entidadBancariaCheque']);

            // Abrir el modal de pago
            $this->mostrarPago = true;
            $this->facturaSeleccionada = $this->ventaTemporal;


        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error al preparar la venta: ' . $e->getMessage());
        }
    }

    // public function procesarPago()
    // {
    //     $this->validate([
    //         'montoPago' => 'required|numeric|min:0.01',
    //         'metodoPago' => 'required|exists:metodos_pago,id',
    //     ]);

    //     // Validaciones específicas por método
    //     $this->validarDatosPorMetodoPago();

    //     try {
    //         $pagoService = new PagoService();
            
    //         $datosAdicionales = [
    //             'referencia' => $this->referenciaPago,
    //             'observaciones' => $this->observacionesPago,
    //             // Agregar más datos según el método de pago
    //         ];

    //         // Agregar datos específicos por método
    //         // DATOS ADICIONALES POR MÉTODO DE PAGO
    //         switch($this->metodoPago) {
    //             case 1: // Efectivo
    //                 // No necesita datos adicionales específicos
    //                 break;
                    
    //             case 2: // Tarjeta Crédito
    //             case 3: // Tarjeta Débito
    //                 $datosAdicionales['datos_tarjeta'] = [
    //                     'ultimos_digitos' => $this->datosTarjeta['numero'],
    //                     'cuotas' => $this->datosTarjeta['cuotas'] ?? 1,
    //                 ];
    //                 break;
                    
    //             case 4: // Transferencia
    //                 $datosAdicionales['entidad_bancaria'] = $this->entidadBancaria;
    //                 $datosAdicionales['fecha_liquidacion'] = now()->addDays(1); // 24-48hs hábiles
    //                 break;
                    
    //             case 5: // Cuenta Corriente
    //                 $datosAdicionales['observaciones'] = 'Venta a cuenta corriente - ' . $this->observacionesPago;
    //                 break;
                    
    //             case 6: // Cheque
    //                 $datosAdicionales['numero_cheque'] = $this->numeroCheque;
    //                 $datosAdicionales['fecha_vencimiento_cheque'] = $this->fechaVencimientoCheque;
    //                 $datosAdicionales['entidad_bancaria'] = $this->entidadBancariaCheque;
    //                 $datosAdicionales['fecha_liquidacion'] = $this->fechaVencimientoCheque; // Se cobra en la fecha de vencimiento
    //                 break;
                         
    //             case 7: // Otros
    //                 $datosAdicionales['observaciones'] = 'Método especial - ' . $this->observacionesPago;
    //                 break;
    //         }

    //         $pago = $pagoService->procesarPago(
    //             $this->facturaSeleccionada->id, // O el ID de la factura creada
    //             $this->metodoPago,
    //             $this->montoPago,
    //             $datosAdicionales
    //         );

    //         // Éxito
    //         $this->cerrarModalPago();
    //         $this->dispatch('venta-create');
    //         $this->resetVenta();

    //     } catch (\Exception $e) {
    //         $this->dispatch('error', message: 'Error al procesar pago: ' . $e->getMessage());
    //     }
    // }

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
                        'datosTarjeta.numero' => 'sometimes|string|min:4|max:16',
                        'datosTarjeta.cuotas' => 'sometimes|integer|min:1|max:36',
                    ]);
                }
                break;
        }
    }

    private function obtenerMetodoPagoId()
    {
        return match($this->forma_pago) {
            'efectivo' => 1,
            'tarjeta_credito' => 2,
            'tarjeta_debito' => 3,
            'transferencia' => 4,
            'cuenta_corriente' => 5,
            'cheques' => 6,
            'otros' => 7,
            default => 1
        };
    }

    private function esPagoElectronico()
    {
        return in_array($this->metodoPago, [2, 3, 4]); // tarjeta_credito, tarjeta_debito, transferencia
    }


    public function render()
    {
        return view('livewire.ventas.venta-create', [
            'clientes' => Cliente::orderBy('nombre')->get(),
        ]);
    }
}
