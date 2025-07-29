<?php

namespace App\Livewire\articulos;

use App\Models\Articulo;
use Livewire\Component;

class ArticuloEdit extends Component
{
    public $articuloId;
    public $articulo, $codigo_proveedor, $descripcion, $marca, $precio, $unidad;

    public function mount($id)
    {
        $this->articuloId = $id;

        $articulo = Articulo::findOrFail($id);

        // Asignar valores a las propiedades del formulario
        $this->articulo = $articulo->articulo;
        $this->codigo_proveedor = $articulo->codigo_proveedor;
        $this->descripcion = $articulo->descripcion;
        $this->marca = $articulo->marca;
        $this->precio = $articulo->precio;
        $this->unidad = $articulo->unidad;
    }

    public function update()
    {
        $this->validate([
            'articulo' => 'required|string|max:255',
            'codigo_proveedor' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'marca' => 'required|string|max:255',
            'precio' => 'nullable|numeric',
            'unidad' => 'required|numeric',
        ]);

        $articulo = Articulo::findOrFail($this->articuloId);

        $articulo->update([
            'articulo' => $this->articulo,
            'codigo_proveedor' => $this->codigo_proveedor,
            'descripcion' => $this->descripcion,
            'marca' => $this->marca,
            'precio' => $this->precio,
            'unidad' => $this->unidad,
        ]);

        session()->flash('message', 'Artículo actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.articulos.articulo-edit');
    }
}


