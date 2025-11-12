<?php

namespace App\Livewire\articulos;

use App\Models\Articulo;
use App\Services\ArticulosService;
use App\Services\ProveedoresService;
use App\Services\RepositoryService;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
class ArticuloIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    protected $articuloService;
    protected $proveedorService;

    public function updatingSearch()
    {
        $this->resetPage(); // resetea la paginación al buscar
    }

    public function placeholder()
    {
        return view('components.loading-page', [
            'variant' => 'inline',
            'message' => 'Cargando articulos...',
            'color' => 'blue',
        ])->render();
    }

    public function mount()
    {
        $this->articuloService = new ArticulosService();
        $this->proveedorService = new ProveedoresService();
    }

    public function render()
    {
        $result = $this->articuloService->get([]);
        $proveedores_result = $this->proveedorService->get(['paginate' => false]);
        if (!$result->successful){
         return view('livewire.articulos.articulo-index', [
             'articulos' => [],
             'proveedores' => [],
         ]);
        }

        return view('livewire.articulos.articulo-index', ['articulos' => $result->data, 'proveedores' => $proveedores_result->data]);
    }
}
