<?php

namespace App\Livewire\Pagos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pago;

class ListaPagos extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'fecha_pago';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'fecha_pago'],
        'sortDirection' => ['except' => 'desc']
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $pagos = Pago::with(['venta', 'metodoPago', 'user'])
            ->when($this->search, function ($query) {
                $query->where('referencia', 'like', '%' . $this->search . '%')
                    ->orWhereHas('venta', function ($q) {
                        $q->where('id', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('metodoPago', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.pagos.lista-pagos', compact('pagos'));
    }
}