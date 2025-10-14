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
    public $fechaDesde;
    public $fechaHasta;
    public $nombreCliente;

    public function updatingFechaDesde() { $this->resetPage(); }
    public function updatingFechaHasta() { $this->resetPage(); }
    public function updatingnombreCliente() { $this->resetPage(); }

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
        $query = Factura::with('cliente');

        // Filtrar por fecha - FORMA CORRECTA
        if ($this->fechaDesde || $this->fechaHasta) {
            if ($this->fechaDesde) {
                $query->whereDate('fecha', '>=', $this->fechaDesde);
            }
            if ($this->fechaHasta) {
                $query->whereDate('fecha', '<=', $this->fechaHasta);
            }
        }

        // Filtro por código o nombre del artículo
        if ($this->nombreCliente) {
            $query->whereHas('cliente', function ($q) {
                $q->where('nombre', 'like', '%' . $this->nombreCliente . '%');
            });
        }

        $ventas = $query->orderByDesc('id')->paginate(10);
        
        return view('livewire.ventas.ventas-index', [
            'ventas' => $ventas,
        ]);
    }
}