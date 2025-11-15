<?php

namespace App\Livewire;

use App\Models\Articulo;
use App\Models\ReferenciaRsf;
use Livewire\Attributes\On;
use Livewire\Component;

class ArticulosModal extends Component
{

    public $showModalDuplicados = false;

    public $coincidenciasArt;
    public $coincidenciasRef;

    public function render()
    {
        return view('livewire.articulos-modal');
    }

    #[On('agregar-articulo')]
    public function agregarArticulo($codigo_barra)
    {
        $this->clearCoincidencias();

        $refItems = ReferenciaRsf::getByCodigo($codigo_barra);

        //FIX
        if ($refItems->count() === 0) {
            $this->addError('codigo_barra', 'El articulo no existe.');
            return;
        };

        $articuloExists = Articulo::getByCodigo($codigo_barra);

        if ($articuloExists->count() > 0) {
            $this->coincidenciasArt = $articuloExists->get();
        }

        if ($refItems->count() === 1 && $articuloExists->count() === 0) {
            $this->dispatch('crear-articulo', articulo_rsf: $refItems->first());
            return;
        }

        $articuloExists = $articuloExists->get();
        $refItems = $refItems->get();


        $this->coincidenciasRef = $refItems;

        if (isset($this->coincidenciasArt)) {
            $filteredRefItems = $refItems->reject(function ($refItem) use ($articuloExists) {
                return $articuloExists->contains(function ($existing) use ($refItem) {
                    return
                        $existing->articulo === $refItem->articulo &&
                        $existing->codigo_fabricante === $refItem->codigo_barra &&
                        $existing->codigo_proveedor === $refItem->codigo_rsf;
                });
            });
            $this->coincidenciasRef = $filteredRefItems;
        }

        if (count($this->coincidenciasRef) === 0 && $articuloExists->count() === 1) {
            $this->dispatch('agregar-articulo-listado', articulo: $articuloExists->first());
            return;
        }

        $this->showModalDuplicados = true;
    }


    public function confirmarSeleccionArt($id)
    {
        $this->showModalDuplicados = false;
        $this->dispatch('selelccionado-articulo', id: $id);
    }

    public function confirmarSeleccionRef($id)
    {
        $this->showModalDuplicados = false;
        $this->dispatch('selelccionado-referencia', id: $id);
    }

    public function clearCoincidencias()
    {
        $this->coincidenciasArt = null;
        $this->coincidenciasRef = null;
    }
}
