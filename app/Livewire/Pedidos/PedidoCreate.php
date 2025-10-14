<?php

namespace App\Livewire\Pedidos;

use App\Models\Articulo;
use App\Models\DetallePedido;
use App\Models\DetalleVenta;
use App\Models\Pedido;
use Livewire\Component;
use Livewire\WithPagination;

class PedidoCreate extends Component
{
    use WithPagination;

    public $seleccionados = [];

    public function guardar()
    {
        $pedido = Pedido::create([
            'fecha' => now(),
        ]);

        foreach ($this->seleccionados as $detalleId) {
            $detalle = DetalleVenta::find($detalleId);
            $detalle->repuesto = 1;
            $detalle->save();

            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'articulo_id' => $detalle->articulo->id,
                'detalle_venta_id' => $detalle->id,
                'cantidad' => $detalle->cantidad,
            ]);
        }

        $this->seleccionados = [];
        $this->dispatch('pedido-creado');
    }

    public function render()
    {

        return view('livewire.pedidos.pedido-create', [
            'detalles_venta' =>  DetalleVenta::where('repuesto', 0)->orderByDesc('id')->paginate(10),
        ]);
    }
}
