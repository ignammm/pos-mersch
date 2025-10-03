<?php

namespace App\Livewire\Presupuestos;

use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\DetallePresupuesto;
use App\Models\Presupuesto;
use App\Models\ReferenciaRsf;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PresupuestoCreate extends Component
{

    public $descripcion_presupuesto, $codigo_barra, $cantidad = 1, $total, $items = [], $articulosModal = [], $modalSeleccionarArticulo = false
    , $stockArticulos, $fecha_validez = 7, $presupuesto_id;


    public function mount($id = null)
    {

        if ($id != null) {

            $presupuesto = Presupuesto::with('detalles.articulo')->findOrFail($id);

            $this->presupuesto_id = $id;
            $this->descripcion_presupuesto = $presupuesto->observaciones;

            $this->items = $presupuesto->detalles->map(function($detalle) {
                return [
                    'articulo_id' => $detalle->articulo_id,
                    'nombre' => $detalle->articulo->articulo,
                    'rubro' => $detalle->articulo->rubro,
                    'marca' => $detalle->articulo->marca,
                    'codigo_proveedor' => $detalle->articulo->codigo_proveedor,
                    'codigo_fabricante' => $detalle->articulo->codigo_fabricante,
                    'cantidad' => (int) $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal' => $this->calcularSubtotal($detalle->precio_unitario, $detalle->cantidad),
                ];
            })->toArray();
            $this->calcularTotal();
            $this->cargarStockArticulos();
        }
    }

    public function cargarStockArticulos()
    {
        $ids = collect($this->items)->pluck('articulo_id')->toArray();

        $stocks = Stock::whereIn('articulo_id', $ids)->pluck('cantidad', 'articulo_id');

        foreach ($this->items as $item) {
            $this->stockArticulos[$item['articulo_id']] = $stocks[$item['articulo_id']] ?? 0;
        }
    }

    public function agregarArticulo()
    {
    
        $this->validate([
            'codigo_barra' => 'required',
            'cantidad' => 'required|numeric|min:1',
        ]);

        $existe = Articulo::where('codigo_proveedor', $this->codigo_barra)
        ->orWhere('codigo_fabricante', $this->codigo_barra)
        ->orWhere('articulo', $this->codigo_barra)
        ->exists();
        
        if ($existe) {
            
            if (Articulo::where('codigo_proveedor', $this->codigo_barra)
                ->orWhere('codigo_fabricante', $this->codigo_barra)
                ->orWhere('articulo', $this->codigo_barra)
                ->count() <= 1)
            {
            
                $articulo = Articulo::where('codigo_proveedor', $this->codigo_barra)
                    ->orWhere('codigo_fabricante', $this->codigo_barra)
                    ->orWhere('articulo', $this->codigo_barra)
                    ->first();
                
                if ($this->stockSuperado($articulo)) {
                    return;
                }
                
                if ($this->verificarExisteEnLista($articulo)) {
                    return;
                }
                
                $this->agregarArticuloLista($articulo);
                
                $this->calcularTotal();
                $this->reset(['codigo_barra', 'cantidad']);
                
                
            } else {
                
                $this->articulosModal = Articulo::where('articulo', $this->codigo_barra)->get();
                $this->modalSeleccionarArticulo = true;
                return;
                
            }
             
        }
        else {
            
            $referenciaRsf = ReferenciaRsf::where('codigo_rsf', $this->codigo_barra)
            ->orWhere('codigo_barra', $this->codigo_barra)
            ->orWhere('articulo', $this->codigo_barra)
            ->first();
            
            if ($referenciaRsf) {
                

                if (ReferenciaRsf::where('articulo', $this->codigo_barra)
                    ->count() > 1) {
                    
                }
                
                $this->crearArticulo($referenciaRsf);
                return;
            }
            $this->addError('codigo_barra', 'El código ingresado no existe.');
            $this->reset(['codigo_barra']);
            return;
        }
        
       
    }

    public function decrementarCantidad($index)
    {
        if (isset($this->items[$index]) && $this->items[$index]['cantidad'] > 1) {
            $this->items[$index]['cantidad']--;
            $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
            $this->stockArticulos[$this->items[$index]['articulo_id']]++;
            $this->calcularTotal();
            return;
        }
        $this->addError('cantidad', 'No es posible tener una cantidad menor a 1.');


    }

    public function incrementarCantidad($index)
    {
        if ($this->stockArticulos[$this->items[$index]['articulo_id']] <= 0) {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock. El stock disponible es de: '. Articulo::find($this->items[$index]['articulo_id'])->stock->cantidad);
            return;
        }

        if (isset($this->items[$index])) {
            $this->items[$index]['cantidad']++;
            $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
            $this->stockArticulos[$this->items[$index]['articulo_id']]--;
            $this->calcularTotal(); // si ya tenés un método de total
        }


    }

    public function eliminarItem($index)
    {
        if (isset($this->items[$index])) {
            $articuloId = $this->items[$index]['articulo_id'];

            // devolver al stock del artículo correcto
            if (isset($this->stockArticulos[$articuloId])) {
                $this->stockArticulos[$articuloId] += $this->items[$index]['cantidad'];
            }

            unset($this->items[$index]);
            $this->items = array_values($this->items); // reindexar
            $this->calcularTotal();
        }

       
    }

    public function confirmarSeleccion($articulo_id)
    {
        $articulo = Articulo::find($articulo_id);
        if ($this->stockSuperado($articulo)) {
            return;
        }
        if ($this->verificarExisteEnLista($articulo)) {
            return;
        }

        $this->agregarArticuloLista($articulo);
    }

    public function stockSuperado($articulo)
    {
        if (isset($this->stockArticulos[$articulo->id])) {

            if ($this->stockArticulos[$articulo->id] < $this->cantidad) {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock. El stock disponible es de: '. $this->stockDisponible($articulo));
            $this->reset(['cantidad']);
            $this->modalSeleccionarArticulo = false;
            return true;
            }
            return false;

        }
        if (Articulo::find($articulo->id)->stock->cantidad < $this->cantidad) {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock. El stock disponible es de: '. $this->stockDisponible($articulo));
            $this->reset(['cantidad']);
            $this->modalSeleccionarArticulo = false;
            return true;
            
        }
        return false;
    }

    public function stockDisponible($articulo)
    {
        if (isset($this->items[$articulo->id])) {
           return $this->stockArticulos[$articulo->id];
        }
        $articulo = Articulo::find($articulo->id);
        return $articulo->stock->cantidad;
    }

    public function verificarExisteEnLista($articulo)
    {
        $index = collect($this->items)->search(fn($item) => $item['articulo_id'] === $articulo->id);

        if ($index === false) {
            return false;
        }

        if ($this->presupuesto_id) {
            
            if ($this->cantidad > $articulo->stock->cantidad) {
                $this->addError('cantidad', 'La cantidad que intenta vender supera el stock.');
                $this->modalSeleccionarArticulo = false;
                return true;
            }

        } else {

           
        }

        $this->items[$index]['cantidad'] += $this->cantidad;
        $this->items[$index]['subtotal'] = $this->calcularSubtotal(
            $this->items[$index]['precio_unitario'],
            $this->items[$index]['cantidad']
        );

        $this->stockArticulos[$this->items[$index]['articulo_id']] -= $this->cantidad;
        $this->calcularTotal();
        $this->reset(['codigo_barra']);

        return true;
    }

    public function agregarArticuloLista($articulo)
    {
        if ($this->stockSuperado($articulo)) {
            return;
        }

        if (!$this->verificarExisteEnLista($articulo)) {
            
            $this->items[] = [
                'articulo_id' => $articulo->id,
                'nombre' => $articulo->articulo,
                'rubro' => $articulo->rubro,
                'marca' => $articulo->marca,
                'codigo_proveedor' => $articulo->codigo_proveedor,
                'codigo_fabricante' => $articulo->codigo_fabricante,
                'cantidad' => $this->cantidad,
                'precio_unitario' => $articulo->precio,
                'subtotal' => $this->calcularSubtotal($articulo->precio, $this->cantidad),
            ];

            $this->stockArticulos[$articulo->id] = Articulo::find($articulo->id)->stock->cantidad - $this->cantidad;
        }

        $this->calcularTotal();
        $this->reset(['codigo_barra', 'cantidad']);
        $this->modalSeleccionarArticulo = false;
    }

    public function calcularTotal()
    {
        $this->total = array_sum(array_column($this->items, 'subtotal'));
    }

    public function calcularSubtotal($precio, $cantidad)
    {
        return ($precio * $cantidad);
    }

    public function guardarPresupuesto()
    {
        $user = Auth::user();

        $this->validate([
            'descripcion_presupuesto' => 'required',
            'fecha_validez' => 'required',
        ]);

        DB::beginTransaction();

        if ($this->presupuesto_id) {
            // edición
            $presupuesto = Presupuesto::find($this->presupuesto_id);
            $presupuesto->update([
                'descripcion_presupuesto' => $this->descripcion_presupuesto,
                'fecha_validez' => now()->addDays($this->fecha_validez),
            ]);

            $presupuesto->detalles()->delete();

            foreach ($this->items as $item) {
                DetallePresupuesto::create([
                    'presupuesto_id' => $presupuesto->id,
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            $this->dispatch('presupuesto-update'); 

            return redirect()->route('presupuestos.show', $this->presupuesto_id);

        } else {

            $presupuesto = Presupuesto::create([
                'numero' => Presupuesto::generarNumero(),
                'usuario_id' => $user->id,
                'fecha_emision' => now(),
                'fecha_validez' => now()->addDays($this->fecha_validez),
                'subtotal' => $this->total,
                'total_estimado' => $this->total,
                'observaciones' => $this->descripcion_presupuesto,
            ]);

            foreach ($this->items as $item) {
                DetallePresupuesto::create([
                    'presupuesto_id' => $presupuesto->id,
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $this->dispatch('presupuesto-create'); 

        }

        DB::commit();
        $this->reset(['items', 'codigo_barra', 'cantidad', 'total', 'descripcion_presupuesto', 'presupuesto_id']);
    }


    public function render()
    {
        return view('livewire.presupuestos.presupuesto-create');
    }
}
