<?php

namespace App\Livewire\Trabajos;

use App\Models\Trabajo;
use Livewire\Component;

class TrabajoEdit extends Component
{
    public $cliente_id;
    public $patente;
    public $nombre_trabajo;
    public $descripcion_trabajo;
    public $codigo_barra;
    public $cantidad;

    public $items = [];
    public $total = 0;

    public $trabajo_id; // <- para saber si es edición

    public function cargarTrabajo($id)
    {
        $trabajo = Trabajo::with('vehiculoCliente', 'detalles')->findOrFail($id);

        $this->trabajo_id = $trabajo->id;
        $this->cliente_id = $trabajo->vehiculoCliente->cliente_id;
        $this->patente = $trabajo->vehiculoCliente->patente;
        $this->nombre_trabajo = $trabajo->nombre;
        $this->descripcion_trabajo = $trabajo->descripcion;

        // si ya tiene artículos cargados en detalle
        $this->items = $trabajo->detalles->map(function($detalle) {
            return [
                'nombre' => $detalle->articulo->articulo,
                'rubro' => $detalle->articulo->rubro,
                'cantidad' => $detalle->cantidad,
                'marca' => $detalle->articulo->marca,
                'precio_unitario' => $detalle->articulo->precio,
                'subtotal' => $detalle->cantidad * $detalle->articulo->precio,
            ];
        })->toArray();

        $this->total = $trabajo->detalles->sum('subtotal');
    }


    public function render()
    {
        return view('livewire.trabajos.trabajo-edit');
    }
}
