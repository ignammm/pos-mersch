<?php

namespace App\Livewire\articulos;

use App\Models\Articulo;
use App\Models\Proveedor;
use App\Models\ReferenciaRsf;
use Livewire\Attributes\On;
use Livewire\Component;

class ArticuloCreate extends Component
{

    public $articulo, $codigo_proveedor, $codigo_fabricante, $rubro, $precio, $descripcion, $marca, $unidad, $proveedor_id, $enlace;

    public function updatedCodigoProveedor($value)
    {
        $referencia = ReferenciaRsf::where('codigo_rsf', $value)->first();

        if ($referencia) {
            $this->articulo = $referencia->articulo;
            $this->codigo_fabricante = $referencia->codigo_barra;
            $this->marca = $referencia->marca_rsf;
            $this->descripcion = $referencia->descripcion;
            $this->rubro = $referencia->tipo_txt;
            $this->precio = round($referencia->precio_lista, 0);
            $this->unidad = $referencia->modulo_venta;
            $this->descripcion = $referencia->descripcion;
            $this->enlace = $referencia->enlace;
        } else {
            $this->addError('codigo_proveedor', 'Articulo no encontrado en referencias. Complete los campos.');
        }
    }

    #[On('selectSuggestion')]
    public function updatedCodigoFabricante($value)
    {
        $referencia = ReferenciaRsf::where('codigo_barra', $value)->first();
        if ($referencia) {
            $this->articulo = $referencia->articulo;
            $this->codigo_proveedor = $referencia->codigo_rsf;
            $this->marca = $referencia->marca_rsf;
            $this->descripcion = $referencia->descripcion;
            $this->rubro = $referencia->tipo_txt;
            $this->precio = round($referencia->precio_lista, 0);
            $this->unidad = $referencia->modulo_venta;
            $this->descripcion = $referencia->descripcion;
            $this->enlace = $referencia->enlace;
        } else {
            $this->addError('codigo_proveedor', 'Articulo no encontrado en referencias. Complete los campos.');
            return;
        }
        $this->resetErrorBag('codigo_proveedor');
    }


    public function submit()
    {

        $this->validate([
            'articulo' => 'required|string|max:100',
            'codigo_proveedor'  => 'required|string|unique:articulos,codigo_proveedor|max:100',
            'codigo_fabricante'  => 'required|string|unique:articulos,codigo_fabricante|max:100',
            'rubro'         => 'required|string|max:200',
            'precio'           => 'required|numeric|min:0',
            'descripcion'      => 'required|string',
            'marca'      => 'required|string|max:100',
            'unidad'    => 'required|integer|between:1,100',
            'proveedor_id' => 'required|integer',
        ]);

        if ($this->enlace){

            $existe = ReferenciaRsf::where('enlace', $this->enlace)
            ->exists();

            if (!$existe){
                $codigo = $this->enlace;
                $this->enlace = Articulo::generarEnlace($codigo);
            }
        }

        Articulo::create([
            'articulo' => $this->articulo,
            'codigo_interno' => Articulo::generarCodigoInterno(),
            'codigo_proveedor' => $this->codigo_proveedor,
            'codigo_fabricante' => $this->codigo_fabricante,
            'rubro'     => $this->rubro,
            'precio'  => $this->precio,
            'descripcion' => $this->descripcion,
            'enlace'   => $this->enlace,
            'marca' => $this->marca,
            'unidad' => $this->unidad,
            'proveedor_id' => $this->proveedor_id,
        ]);

        session()->flash('message', 'Articulo creado correctamente!');
        $this->reset();
        $this->dispatch('articulo-creado');

    }

    public function render()
    {
        $proveedores = Proveedor::all();

        return view('livewire.articulos.articulo-create', [
            'proveedores' => $proveedores,
        ]);
    }
}
