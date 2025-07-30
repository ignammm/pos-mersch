<?php

namespace App\Livewire\articulos;

use App\Models\Articulo;
use App\Models\Proveedor;
use Livewire\Component;
use Illuminate\Validation\Rule;

class ArticuloEdit extends Component
{
    public $articuloId;
    public $articulo, $codigo_proveedor, $descripcion, $marca, $precio, $unidad, $rubro, $proveedor_id, $codigo_fabricante, $enlace;
    public $confirmingDeletion = false;

    public function mount($id)
    {
        $this->articuloId = $id;

        $articulo = Articulo::findOrFail($id);

        // Asignar valores a las propiedades del formulario
        $this->articulo = $articulo->articulo;
        $this->codigo_proveedor = $articulo->codigo_proveedor;
        $this->codigo_fabricante = $articulo->codigo_fabricante;
        $this->enlace = $articulo->enlace;
        $this->descripcion = $articulo->descripcion;
        $this->rubro = $articulo->rubro;
        $this->marca = $articulo->marca;
        $this->precio = $articulo->precio;
        $this->unidad = $articulo->unidad;
        $this->proveedor_id = $articulo->proveedor_id;
    }

    public function update()
    {
        $this->validate([
            'articulo' => 'required|string|max:100',
            'codigo_proveedor' => [
                'required',
                'string',
                'max:100',
                Rule::unique('articulos', 'codigo_proveedor')->ignore($this->articuloId),
            ],
            'codigo_fabricante' => 'required',
            'rubro'         => 'required|string|max:200',
            'precio'           => 'required|numeric|min:0',
            'descripcion'      => 'required|string',
            'marca'      => 'required|string|max:100',
            'unidad'    => 'required|integer|between:1,100',
            'proveedor_id' => 'required|integer',
        ]);

        $articulo = Articulo::findOrFail($this->articuloId);

        $articulo->update([
            'articulo' => $this->articulo,
            'codigo_proveedor' => $this->codigo_proveedor,
            'codigo_fabricante' => $this->codigo_fabricante,
            'enlace' => $this->enlace,
            'rubro'     => $this->rubro,
            'precio'  => $this->precio,
            'descripcion' => $this->descripcion,
            'marca' => $this->marca,
            'unidad' => $this->unidad,
            'proveedor_id' => $this->proveedor_id,
        ]);

        session()->flash('message', 'Artículo actualizado correctamente.');
    }

    public function delete()
    {
        $articulo = Articulo::findOrFail($this->articuloId);
        $articulo->delete();

        session()->flash('message', 'Artículo eliminado correctamente.');

        return $this->redirect(route('articulos.index'));
    }

    public function render()
    {
        $proveedores = Proveedor::all();

        return view('livewire.articulos.articulo-edit', [
            'proveedores' => $proveedores,
        ]);
    }
}


