<?php

namespace App\Livewire\Ventas;

use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Factura;
use App\Models\Pedido;
use App\Models\Stock;
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

    public function guardar()
    {
        $user = Auth::user();

        $this->validate([
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        
        $venta = Factura::create([
            'cliente_id' => $this->cliente_id,
            'user_id' => $user->id,
            'tipo_comprobante' => $this->tipo_comprobante,
            'numero' => Factura::numeroComprobante($this->tipo_comprobante),
            'monto_original' => $this->total,
            'descuento_aplicado' => $this->descuento_factura,
            'monto_final' => $this->calcularTotal(),
            'forma_pago' => $this->forma_pago,
            'fecha' => now(),
            'total' => $this->total,
        ]);

        foreach ($this->items as $item) {
            $detalle = DetalleVenta::create([
                'factura_id' => $venta->id,
                'articulo_id' => $item['articulo_id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'descuento_aplicado' => $item['descuento_unitario'] ?? 0,
                'subtotal' => $item['subtotal'],
            ]);

            $detalle->movimientos()->create([
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                    'tipo' => 'salida',
                    'motivo' => 'venta',
                    'estado_reposicion' => 'pendiente',
             ]);

            // Descontar stock de tabla stock
            $stock = Stock::where('articulo_id', $item['articulo_id'])->first();

            if ($stock) {
                $stock->cantidad = max(0, $stock->cantidad - $item['cantidad']);
                $stock->save();
            }
        }
        

        DB::commit();

        $this->dispatch('venta-create'); 
        $this->reset(['cliente_id', 'forma_pago', 'items', 'codigo_barra', 'cantidad', 'descuento', 'total']);
    }


    public function render()
    {
        return view('livewire.ventas.venta-create', [
            'clientes' => Cliente::orderBy('nombre')->get(),
        ]);
    }
}
