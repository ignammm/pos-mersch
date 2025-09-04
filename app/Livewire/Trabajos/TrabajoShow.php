<?php

namespace App\Livewire\Trabajos;

use App\Models\DetalleTrabajo;
use App\Models\Trabajo;
use Livewire\Attributes\On;
use Livewire\Component;

class TrabajoShow extends Component
{

    public Trabajo $trabajo;

    public function mount($id)
    {
        $this->trabajo = Trabajo::findOrFail($id);
    }

    public function eliminarTrabajo()
    {
        $this->trabajo->detalles()->delete();

        Trabajo::findOrFail($this->trabajo->id)->delete();

        session()->flash('message', 'Trabajo eliminado correctamente');
        
        return redirect()->route('trabajos.index');
    }


    public function render()
    {
        $detalles_trabajo = DetalleTrabajo::with('articulo')
        ->where('trabajo_id', $this->trabajo->id)
        ->where('activo', true)
        ->get();
        return view('livewire.trabajos.trabajo-show', [
            'detalles_trabajo' => $detalles_trabajo,
        ]);
    }
}
