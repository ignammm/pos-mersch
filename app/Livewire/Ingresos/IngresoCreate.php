<?php

namespace App\Livewire\Ingresos;

use App\Models\Articulo;
use App\Models\DetalleIngresos;
use App\Models\Ingreso;
use App\Models\Proveedor;
use App\Models\ReferenciaRsf;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IngresoCreate extends Component
{
    public $proveedor_id, $rubro, $tipo_comprobante, $numero_comprobante, $fecha, $total = 0, $codigo_proveedor, $codigo_fabricante;
    public $cantidad = 1;
    public $codigo_barra;
    public $items = [];
    public $referenciaSeleccionada;
    public $mostrarModal = false;


    public function agregarArticulo()
    {
        $this->validate([
            'codigo_barra' => 'required',
            'cantidad' => 'required|numeric|min:1',
        ]);

        $existe = ReferenciaRsf::where('codigo_rsf', $this->codigo_barra)
        ->orWhere('codigo_barra', $this->codigo_barra)
        ->exists();

        if (!$existe) {
            $this->addError('codigo_barra', 'El código ingresado no existe en la base de datos, deberá crearlo manualmente.');
            return;
        }

        $articulo = Articulo::where('codigo_proveedor', $this->codigo_barra)
        ->orWhere('codigo_fabricante', $this->codigo_barra)
        ->first();

        if (!$articulo) {
            $referencia = ReferenciaRsf::where('codigo_rsf', $this->codigo_barra)
            ->orWhere('codigo_barra', $this->codigo_barra)
            ->first();

            if ($referencia) {
                $this->referenciaSeleccionada = $referencia->toArray();
                $this->mostrarModal = true;
            } else {
                $this->addError('codigo_barra', 'Código no encontrado en referencias.');
            }

            return;
        }

        
        if ($articulo->codigo_proveedor === $this->codigo_barra) {
            $this->codigo_proveedor = $this->codigo_barra;
            $this->codigo_fabricante = $articulo->codigo_fabricante;
        }
        else {
            $this->codigo_fabricante = $this->codigo_barra;
            $this->codigo_proveedor = $articulo->codigo_proveedor;
        }

        if ($articulo) {
            // Buscar si ya existe el artículo en los items
            foreach ($this->items as $index => $item) {
                if ($item['codigo_proveedor'] === $this->codigo_barra or $item['codigo_fabricante'] === $this->codigo_barra) {
                    // Sumar cantidad y actualizar subtotal
                    $this->items[$index]['cantidad'] += $this->cantidad;
                    $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];

                    $this->calcularTotal();

                    // Limpiar input y salir
                    $this->codigo_barra = '';
                    $this->cantidad = 1;
                    return;
                }
            }

            // Si no estaba, agregar nuevo artículo
            $this->items[] = [
                'articulo_id' => $articulo->id,
                'nombre' => $articulo->articulo,
                'rubro' => $articulo->rubro,
                'codigo_proveedor' => $this->codigo_proveedor,
                'codigo_fabricante' => $this->codigo_fabricante,
                'cantidad' => $this->cantidad,
                'precio_unitario' => $articulo->precio,
                'subtotal' => ($this->cantidad * $articulo->precio),
            ];

            $this->calcularTotal();
        }

        $this->codigo_barra = '';
        $this->cantidad = 1;
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

        $this->mostrarModal = false;
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
                'tipo_comprobante' => $this->tipo_comprobante,
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


    public function render()
    {
        return view('livewire.ingresos.ingreso-create', [
            'proveedores' => Proveedor::all(),
        ]);
    }

}
