<?php

namespace App\Livewire\Ingresos;

use App\Livewire\articulos\ArticuloCreate;
use App\Models\Articulo;
use App\Models\DetalleIngresos;
use App\Models\Ingreso;
use App\Models\Proveedor;
use App\Models\ReferenciaRsf;
use App\Requests\ArticuloGetRequest;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class IngresoCreate extends Component
{
    public $proveedor_id = 1, $rubro, $numero_comprobante, $fecha, $total = 0, $codigo_proveedor, $codigo_fabricante;
    public $cantidad = 1;
    public $codigo_barra;
    public $items = [];
    public $referenciaSeleccionada;

    public $existen_duplicados = false;
    public $articulosDuplicados = [];
    public $mostrarModalDuplicados = false;

    public $coincidenciasArt;
    public $coincidenciasRef;


    public function agregarArticulo()
    {
        $this->validate([
            'codigo_barra' => 'required',
            'cantidad' => 'required|numeric|min:1',
        ]);

        $this->dispatch('agregar-articulo', codigo_barra: $this->codigo_barra);
    }


    private function mostrarModalDuplicados()
    {
        $this->mostrarModalDuplicados = true;
        $this->existen_duplicados = true;
    }

    private function sumarCantidadSiExiste($articulo)
    {
        foreach ($this->items as $index => $item) {
            if (
                $item['articulo_id'] === $articulo->id
            ) {
                $this->items[$index]['cantidad'] += $this->cantidad;
                $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
                $this->calcularTotal();
                $this->codigo_barra = '';
                $this->cantidad = 1;

                $this->mostrarModalDuplicados = false;
                $this->coincidenciasArt = '';
                $this->coincidenciasRef = '';
                $this->referenciaSeleccionada = null;

                return true; // ya existía y se sumó
            }
        }

        return false; // no existía
    }


    #[On('agregar-articulo-listado')]
    public function agregarArticuloListado($articulo)
    {
        $articulo = (object) $articulo;
        $this->items[] = [
            'articulo_id' => $articulo->id,
            'nombre' => $articulo->articulo,
            'rubro' => $articulo->rubro,
            'marca' => $articulo->marca,
            'codigo_proveedor' => $articulo->codigo_proveedor,
            'codigo_fabricante' => $articulo->codigo_fabricante,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $articulo->precio,
            'subtotal' => ($this->cantidad * $articulo->precio),
        ];

        $this->calcularTotal();
        $this->limpiarCampos();
    }

    private function limpiarCampos()
    {
        $this->codigo_barra = '';
        $this->cantidad = 1;
        
        $this->mostrarModalDuplicados = false;
        $this->coincidenciasArt = '';
        $this->coincidenciasRef = '';
        $this->referenciaSeleccionada = null;

    }

    #[On('selelccionado-articulo')]
    public function confirmarSeleccionArt($id)
    {
        $articulo = Articulo::find($id);
        if ($this->sumarCantidadSiExiste($articulo)) {
            return;
        }
        $this->agregarArticuloListado($articulo);
    }

    #[On('selelccionado-referencia')]
    public function confirmarSeleccionRef($id)
    {
        $this->crearArticulo(ReferenciaRsf::find($id));
    }

    #[On('crear-articulo')]
    public function crearArticulo($articulo_rsf)
    {
        $articulo_rsf = (object) $articulo_rsf;
        
        if (!Articulo::where('codigo_proveedor', $articulo_rsf->codigo_rsf)->exists()) {
            $articulo = Articulo::create([
                'articulo' => $articulo_rsf->articulo,
                'codigo_interno' => Articulo::generarCodigoInterno(),
                'codigo_proveedor' => $articulo_rsf->codigo_rsf,
                'codigo_fabricante' => $articulo_rsf->codigo_barra,
                'rubro' => $articulo_rsf->tipo_txt,
                'precio' => round($articulo_rsf->precio_lista, 0),
                'marca' => $articulo_rsf->marca_rsf,
                'descripcion' => $articulo_rsf->descripcion,
                'enlace' => $articulo_rsf->enlace,
                'unidad' => $articulo_rsf->modulo_venta,
                'proveedor_id' => $this->proveedor_id,
            ]);

            $this->agregarArticuloListado($articulo);

            return;
        }

        $this->addError('codigo_barra', 'El articulo que intenta crear ya ha sido creado.');
        $this->mostrarModalDuplicados = false;
        $this->coincidenciasArt = '';
        $this->coincidenciasRef = '';
        $this->referenciaSeleccionada = null;
    }


    public function confirmarCreacionArticulo()
    {
        Articulo::create([
            'articulo' => $this->referenciaSeleccionada['articulo'] ?? null,
            'codigo_interno' => Articulo::generarCodigoInterno(),
            'codigo_proveedor' => $this->referenciaSeleccionada['codigo_rsf'] ?? null,
            'codigo_fabricante' => $this->referenciaSeleccionada['codigo_barra'] ?? null,
            'rubro' => $this->referenciaSeleccionada['tipo_txt'] ?? null,
            'precio' => round($this->referenciaSeleccionada['precio_lista'] ?? 0, 0),
            'marca' => $this->referenciaSeleccionada['marca_rsf'] ?? null,
            'descripcion' => $this->referenciaSeleccionada['descripcion'] ?? null,
            'enlace' => $this->referenciaSeleccionada['enlace'] ?? null,
            'unidad' => $this->referenciaSeleccionada['modulo_venta'] ?? null,
            'proveedor_id' => $this->proveedor_id,
        ]);

        $this->agregarArticulo();

        $this->mostrarModalDuplicados = false;
        $this->referenciaSeleccionada = null;

        $this->dispatch('articulo-creado');
    }


    public function calcularTotal()
    {
        $this->total = 0;
        foreach ($this->items as &$item) {
            $item['subtotal'] = $item['cantidad'] * $item['precio_unitario'];
            $this->total += $item['subtotal'];
        }
    }

    public function guardar()
    {
        $this->validate([
            'proveedor_id' => 'required',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $ingreso = Ingreso::create([
                'proveedor_id' => $this->proveedor_id,
                'numero_comprobante' => $this->numero_comprobante,
                'fecha' => now(),
                'total' => $this->total,
            ]);

            foreach ($this->items as $item) {
                DetalleIngresos::create([
                    'ingreso_id' => $ingreso->id,
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Actualizar stock
                $articulo = Articulo::find($item['articulo_id']);
                $stock = $articulo->stock()->firstOrCreate([], [
                    'cantidad' => 0,
                ]);

                $stock->cantidad += $item['cantidad'];
                $stock->save();
            }

            DB::commit();

            $this->dispatch('ingreso-create');

            $this->reset();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function eliminarItem($index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Reindexa el array
        }

        $this->calcularTotal();
    }

    public function placeholder(){
        return view('components.loading-page', [
            'variant' => 'inline',
            'message' => 'Cargando articulos...',
            'color' => 'blue',
        ])->render();
    }


    public function render()
    {
        return view('livewire.ingresos.ingreso-create', [
            'proveedores' => Proveedor::all(),
        ]);
    }
}
