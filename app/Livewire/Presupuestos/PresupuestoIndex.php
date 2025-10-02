<?php

namespace App\Livewire\Presupuestos;

use App\Models\Presupuesto;
use Livewire\Component;
use Livewire\WithPagination;

class PresupuestoIndex extends Component
{
    use WithPagination;

    public $search = '', $search_fecha_inicio, $search_fecha_fin;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $presupuestos = Presupuesto::query()
            ->where('activo', true)
            ->when($this->search, function ($query) {
                $query->where('observaciones', 'like', "%{$this->search}%");
            })
            ->when($this->search_fecha_inicio && $this->search_fecha_fin, function ($query) {
                $query->whereBetween('fecha_emision', [$this->search_fecha_inicio, $this->search_fecha_fin]);
            })
            ->latest()
            ->paginate(8);


        return view('livewire.presupuestos.presupuesto-index', compact('presupuestos'));
    }
}
