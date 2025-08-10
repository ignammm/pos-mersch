<?php

namespace App\Livewire\Pedidos;

use App\Models\DetalleVenta;
use App\Models\Pedido;
use Livewire\Component;
use Livewire\WithPagination;

class PedidosIndex extends Component
{
    use WithPagination;

    public $fechaDesde;
    public $fechaHasta;
    public $codigoArticulo;

    public function updatingFechaDesde() { $this->resetPage(); }
    public function updatingFechaHasta() { $this->resetPage(); }
    public function updatingCodigoArticulo() { $this->resetPage(); }

    public function render()
    {
        // Obtener IDs de artículos que están guardados como pedidos
        $articulosPedidos = DetalleVenta::where('repuesto', 1)->pluck('articulo_id');

        $query = DetalleVenta::with(['articulo', 'factura'])
            ->whereIn('articulo_id', $articulosPedidos);

        // Filtrar por fecha
        if ($this->fechaDesde || $this->fechaHasta) {
            $query->whereHas('factura', function ($q) {
                if ($this->fechaDesde) {
                    $q->whereDate('fecha', '>=', $this->fechaDesde);
                }
                if ($this->fechaHasta) {
                    $q->whereDate('fecha', '<=', $this->fechaHasta);
                }
            });
        }

        // Filtro por código o nombre del artículo
        if ($this->codigoArticulo) {
            $query->whereHas('articulo', function ($q) {
                $q->where('articulo', 'like', '%' . $this->codigoArticulo . '%');
            });
        }

        $detalles = $query->orderByDesc('id')->paginate(10);

        return view('livewire.pedidos.pedidos-index', [
            'detalles' => $detalles,
        ]);
    }
}
