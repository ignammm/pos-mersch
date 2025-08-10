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
    public $articulosModal = [];

    public function agregarArticulo()
    {
        $this->validate([
            'codigo_barra' => 'required',
            'cantidad' => 'required|numeric|min:1',
            'descuento' => 'numeric|min:0',
        ]);

        if (Articulo::where('articulo', $this->codigo_barra)->count() > 1)
        {
            $articulosModal = Articulo::where('articulo', $this->codigo_barra)->get();
            $modalSeleccionarArticulo = true;
        }

        $articulo = Articulo::where('codigo_proveedor', $this->codigo_barra)
            ->orWhere('codigo_fabricante', $this->codigo_barra)
            ->orWhere('articulo', $this->codigo_barra)
            ->first();

        if (!$articulo) {
            $this->addError('codigo_barra', 'El código ingresado no existe.');
            return;
        }

        if($articulo->stock->cantidad < $this->cantidad)
        {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock.');
            return;
        }

        foreach ($this->items as $index => $item) {
            if (
                $item['codigo_proveedor'] === $this->codigo_barra ||
                $item['codigo_fabricante'] === $this->codigo_barra ||
                $item['nombre'] === $this->codigo_barra
            ) {
                if(($this->items[$index]['cantidad'] + $this->cantidad) > $articulo->stock->cantidad)
                {
                    $this->addError('cantidad', 'La cantidad que intenta vender supera el stock.');
                    return;
                }
                $this->items[$index]['cantidad'] += $this->cantidad;
                $this->items[$index]['subtotal'] = $this->calcularSubtotal(
                    $this->items[$index]['precio_unitario'],
                    $this->items[$index]['cantidad'],
                    $this->items[$index]['descuento_unitario']
                );
                $this->calcularTotal();
                $this->reset(['codigo_barra', 'cantidad', 'descuento']);
                return;
            }
        }

        $this->items[] = [
            'articulo_id' => $articulo->id,
            'nombre' => $articulo->articulo,
            'rubro' => $articulo->rubro,
            'codigo_proveedor' => $articulo->codigo_proveedor,
            'codigo_fabricante' => $articulo->codigo_fabricante,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->descuentoUnitario($articulo->precio, $this->descuento),
            'descuento_unitario' => $this->descuento,
            'subtotal' => $this->calcularSubtotal($this->descuentoUnitario($articulo->precio, $this->descuento), $this->cantidad),
        ];

        $this->calcularTotal();
        $this->reset(['codigo_barra', 'cantidad', 'descuento']);
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
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->calcularTotal();
        }
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
            DetalleVenta::create([
                'factura_id' => $venta->id,
                'articulo_id' => $item['articulo_id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'descuento_aplicado' => $item['descuento_unitario'] ?? 0,
                'subtotal' => $item['subtotal'],
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
