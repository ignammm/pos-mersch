<?php

namespace App\Livewire\Trabajos;

use App\Models\Trabajo;
use Livewire\Component;
use Livewire\WithPagination;

class TrabajoIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $trabajos = Trabajo::with(['vehiculoCliente.cliente', 'vehiculoCliente.vehiculo'])
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', "%{$this->search}%")
                      ->orWhereHas('vehiculoCliente.cliente', function ($q) {
                          $q->where('nombre', 'like', "%{$this->search}%");
                      })
                      ->orWhereHas('vehiculoCliente.vehiculo', function ($q) {
                          $q->where('marca', 'like', "%{$this->search}%")
                            ->orWhere('modelo', 'like', "%{$this->search}%");
                      });
            })
            ->where('estado', '!=', 'cancelado')
            ->latest()
            ->paginate(10);

        return view('livewire.trabajos.trabajo-index', compact('trabajos'));
    }
}
