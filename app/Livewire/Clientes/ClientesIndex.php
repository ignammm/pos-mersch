<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class ClientesIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Para que mantenga el search al cambiar de página
    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        // Resetea a la primera página cuando se cambia el texto del buscador
        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->when($this->search, fn($query) =>
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('telefono', 'like', '%' . $this->search . '%')
                    ->orWhere('cuit_cuil', 'like', '%' . $this->search . '%')
            )
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.clientes.clientes-index', compact('clientes'));
    }
}
