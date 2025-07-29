<?php

namespace App\Livewire;

use App\Models\Articulo;
use Livewire\Component;
use Livewire\WithPagination;

class ArticuloIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage(); // resetea la paginación al buscar
    }

    public function render()
    {
        $articulos = Articulo::query()
            ->where('articulo', 'like', '%' . $this->search . '%')
            ->orWhere('codigo_interno', 'like', '%' . $this->search . '%')
            ->orWhere('codigo_proveedor', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.articulo-index', compact('articulos'));
       
    }
}
