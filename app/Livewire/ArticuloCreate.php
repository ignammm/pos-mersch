<?php

namespace App\Livewire;

use App\Models\Articulo;
use App\Models\Proveedor;
use App\Models\ReferenciaRsf;
use Livewire\Component;

class ArticuloCreate extends Component
{

    public $articulo, $codigo_proveedor, $rubro, $precio, $descripcion, $marca, $unidad, $proveedor_id;
    
    public function updatedCodigoProveedor($value)
    {
        $referencia = ReferenciaRsf::where('codigo_rsf', $value)->first();

        if ($referencia) {
            $this->articulo = $referencia->articulo;
            $this->marca = $referencia->marca_rsf;
            $this->descripcion = $referencia->descripcion;
            $this->rubro = $referencia->rubro;
            $this->precio = $referencia->precio_lista;
            $this->unidad = $referencia->modulo_venta;
            $this->descripcion = $referencia->descripcion;
        }
    }
    public function submit()
    {
        $this->validate([
            'articulo' => 'required|string|max:100',
            'codigo_proveedor'  => 'required|string|unique:articulos,codigo_proveedor|max:100',
            'rubro'         => 'required|string|max:200',
            'precio'           => 'required|numeric|min:0',
            'descripcion'      => 'required|string',
            'marca'      => 'required|string|max:100',
            'unidad'    => 'required|integer|between:1,100',
            'proveedor_id' => 'required|integer',
        ]);

        Articulo::create([
            'articulo' => $this->articulo,
            'codigo_interno' => Articulo::generarCodigoInterno(),
            'codigo_proveedor' => $this->codigo_proveedor,
            'precio'  => $this->precio,
            'descripcion' => $this->descripcion,
            'marca' => $this->marca,
            'unidad' => $this->unidad,
           
        ]);

        session()->flash('message', 'Articulo creado correctamente!');
        $this->reset();
    }

    public function render()
    {
        $proveedores = Proveedor::all();

        return view('livewire.articulo-create', [
            'proveedores' => $proveedores,
        ]);
    }
}
