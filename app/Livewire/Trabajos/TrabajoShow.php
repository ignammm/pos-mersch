<?php

namespace App\Livewire\Trabajos;

use App\Models\DetalleTrabajo;
use App\Models\Trabajo;
use Livewire\Component;

class TrabajoShow extends Component
{

    public Trabajo $trabajo;

    public function mount($id)
    {
        $this->trabajo = Trabajo::findOrFail($id);
    }

    public function render()
    {
        $detalles_trabajo = DetalleTrabajo::with('articulo')
        ->where('trabajo_id', $this->trabajo->id)
        ->get();
        return view('livewire.trabajos.trabajo-show', [
            'detalles_trabajo' => $detalles_trabajo,
        ]);
    }
}
