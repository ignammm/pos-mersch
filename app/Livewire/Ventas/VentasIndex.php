<?php

namespace App\Livewire\Ventas;

use Livewire\Component;
use App\Models\Factura;
use App\Models\DetalleVenta;
use Livewire\WithPagination;

class VentasIndex extends Component
{
    use WithPagination;

    public $ventaSeleccionada = null;
    public $mostrarDetalle = false;

    public function verDetalle($facturaId)
    {
        $this->ventaSeleccionada = Factura::with('detalles.articulo')->find($facturaId);
        $this->mostrarDetalle = true;
    }

    public function cerrarDetalle()
    {
        $this->mostrarDetalle = false;
        $this->ventaSeleccionada = null;
    }

    public function render()
    {
        return view('livewire.ventas.ventas-index', [
            'ventas' => Factura::with('cliente')->orderByDesc('fecha')->paginate(8),
        ]);
    }
}