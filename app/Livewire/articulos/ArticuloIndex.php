<?php

namespace App\Livewire\articulos;

use App\Models\Articulo;
use Livewire\Component;
use Livewire\WithPagination;

class ArticuloIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage(); // resetea la paginación al buscar
    }

    public function render()
    {
        $articulos = Articulo::query()
        ->where(function($query) {
            $query->where('articulo', 'like', '%' . $this->search . '%')
                ->orWhere('codigo_fabricante', 'like', '%' . $this->search . '%')
                ->orWhere('codigo_proveedor', 'like', '%' . $this->search . '%')
                ->orWhere('rubro', 'like', '%' . $this->search . '%')
                ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                ->orWhere('marca', 'like', '%' . $this->search . '%');
        })
        ->orderBy('id', 'desc')
        ->paginate(9);


        return view('livewire.articulos.articulo-index', compact('articulos'));
       
    }
}
