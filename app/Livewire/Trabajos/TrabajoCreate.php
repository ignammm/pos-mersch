<?php

namespace App\Livewire\Trabajos;

use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\DetalleTrabajo;
use App\Models\DetalleVenta;
use App\Models\Factura;
use App\Models\Stock;
use App\Models\Trabajo;
use App\Models\Vehiculo;
use App\Models\VehiculoCliente;
use App\Models\VehiculoReferencia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TrabajoCreate extends Component
{
    public $cliente_id,  $vehiculos_cliente = [], $patente, $codigo_barra, $cantidad = 1, $items = [], $items_originales = [], $total,
    $marca, $marcas = [], $clientes, $modelos = [], $modelo, $anio, $descripcion, $patente_modal, $mostrarModalVehiculo = false;
    public $articulosModal = [], $modalSeleccionarArticulo = false;
    public $nombre_trabajo, $descripcion_trabajo;
    public $trabajo_id;

    public function mount($id = null)
    {

        if ($id != null) {

            $trabajo = Trabajo::with('vehiculoCliente.cliente','vehiculoCliente.vehiculo', 'detalles.articulo')->findOrFail($id);

            $this->trabajo_id = $trabajo->id;
            $this->cliente_id = $trabajo->vehiculoCliente->cliente->id;
            $this->vehiculos_cliente = VehiculoCliente::where('cliente_id', $this->cliente_id)->get();
            $this->patente = $trabajo->vehiculoCliente->id;
            $this->patente = $trabajo->vehiculoCliente->id;
            $this->nombre_trabajo = $trabajo->nombre;
            $this->descripcion_trabajo = $trabajo->descripcion;

            // si ya tiene artículos cargados en detalle
            $this->items = $trabajo->detalles->map(function($detalle) {
                return [
                    'articulo_id' => $detalle->articulo_id,
                    'nombre' => $detalle->articulo->articulo,
                    'rubro' => $detalle->articulo->rubro,
                    'marca' => $detalle->articulo->marca,
                    'codigo_proveedor' => $detalle->articulo->codigo_proveedor,
                    'codigo_fabricante' => $detalle->articulo->codigo_fabricante,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->articulo->precio,
                    'subtotal' => $this->calcularSubtotal($detalle->articulo->precio, $detalle->cantidad),
                ];
            })->toArray();
            $this->items_originales = $this->items;
            $this->calcularTotal();
        }
    }


    public function updatedClienteId() 
    { 
        if(VehiculoCliente::where('cliente_id', $this->cliente_id)
        ->exists())
        {
            $this->vehiculos_cliente = VehiculoCliente::where('cliente_id', $this->cliente_id)
            ->get();
        }
        else
        {
            $this->addError('cliente', 'Este cliente no tiene ningun vehiculo a su nombre, asignele uno.');
            return;
        }
    }

    public function updatedMarca() 
    { 
        $this->modelos = VehiculoReferencia::where('marca', $this->marca)
            ->get();
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
            
            $this->addError('codigo_barra', 'El código ingresado no existe.');
            $this->reset(['codigo_barra']);
            return;
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
        $cantidadGuardada = $this->trabajo_id === null
        ? (collect($this->items)->firstWhere('articulo_id', $articulo->id)['cantidad'] ?? 0)
        : 0;

        if ((Articulo::find($articulo->id)->stock->cantidad - $cantidadGuardada) < $this->cantidad) {
            $this->addError('cantidad', 'La cantidad que intenta vender supera el stock. El stock disponible es de: '. $this->stockDisponible($articulo) . '.');
            $this->reset(['cantidad']);
            $this->modalSeleccionarArticulo = false;
            return true;
        }
        return false;
    }

    public function stockDisponible($articulo)
    {
        $articulo = Articulo::find($articulo->id);
        return $articulo->stock->cantidad;
    }

    public function verificarExisteEnLista($articulo)
    {
        $index = collect($this->items)->search(fn($item) => $item['articulo_id'] === $articulo->id);

        if ($index === false) {
            return false;
        }

        if ($this->trabajo_id) {
            
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

        $this->calcularTotal();
        $this->reset(['codigo_barra']);

        return true;
    }


    public function calcularSubtotal($precio, $cantidad)
    {
        return ($precio * $cantidad);
    }

    public function calcularTotal()
    {
        $this->total = array_sum(array_column($this->items, 'subtotal'));
    }

    public function eliminarItem($index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->calcularTotal();
        }
    }

    public function confirmarVehiculoCliente()
    {
        $this->validate([
            'marca' => 'required',
            'modelo' => 'required',
            'anio' => 'required|numeric|min:1900',
            'patente_modal' => 'required',
        ]);

        $vehiculo = Vehiculo::create([
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'año' => $this->anio,
            'descripcion' => $this->descripcion
        ]);

        $vehiculoCliente = VehiculoCliente::create([
            'cliente_id' => $this->cliente_id,
            'vehiculo_id' => $vehiculo->id,
            'patente' => $this->patente_modal
        ]);

        $this->patente = $vehiculoCliente->patente;
        $this->vehiculos_cliente = VehiculoCliente::where('cliente_id', $this->cliente_id)->get();

        $this->mostrarModalVehiculo = false;

        return;
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
        }

        $this->calcularTotal();
        $this->reset(['codigo_barra', 'cantidad']);
        $this->modalSeleccionarArticulo = false;
    }

    public function agregarVehiculoCliente()
    {
        $this->mostrarModalVehiculo = true;
        $this->marcas = VehiculoReferencia::selectRaw('MIN(id) as id, marca')
            ->groupBy('marca')
            ->orderBy('marca')
            ->get();
        return;
    }

    public function compararItems()
    {
        $originales = collect($this->items_originales)->keyBy('articulo_id');
        $nuevos = collect($this->items)->keyBy('articulo_id');

        // 1) Artículos eliminados -> devolver stock
        $eliminados = $originales->diffKeys($nuevos);
        foreach ($eliminados as $item) {
            Stock::where('articulo_id', $item['articulo_id'])
                ->increment('cantidad', $item['cantidad']);
        }

        // 2) Artículos nuevos -> descontar stock
        $agregados = $nuevos->diffKeys($originales);
        foreach ($agregados as $item) {
            Stock::where('articulo_id', $item['articulo_id'])
                ->decrement('cantidad', $item['cantidad']);
        }

        // 3) Artículos que se mantienen -> ver si cambió la cantidad
        $comunes = $nuevos->intersectByKeys($originales);
        foreach ($comunes as $id => $itemNuevo) {
            $itemViejo = $originales[$id];

            if ($itemNuevo['cantidad'] != $itemViejo['cantidad']) {
                $diferencia = $itemNuevo['cantidad'] - $itemViejo['cantidad'];

                if ($diferencia > 0) {
                    // pidió más -> restar al stock
                    Stock::where('articulo_id', $id)->decrement('cantidad', $diferencia);
                } else {
                    // quitó algunos -> devolver al stock
                    Stock::where('articulo_id', $id)->increment('cantidad', abs($diferencia));
                }
            }
        }
    }

    public function guardarTrabajo()
    {
        $user = Auth::user();

        $this->validate([
            'items' => 'required|array|min:1',
            'nombre_trabajo' => 'required',
        ]);

        DB::beginTransaction();

        if ($this->trabajo_id) {
            // edición
            $trabajo = Trabajo::find($this->trabajo_id);
            $trabajo->update([
                'nombre' => $this->nombre_trabajo,
                'vehiculo_cliente_id' => $this->patente,
                'descripcion' => $this->descripcion_trabajo,
            ]);

            // Actualizar detalles (puedes borrar y volver a insertar, o comparar)
            $trabajo->detalles()->delete();

            
            foreach ($this->items as $item) {
                DetalleTrabajo::create([
                    'trabajo_id' => $this->trabajo_id,
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                ]);
                
            }

            $this->compararItems();

            DB::commit();

            $this->dispatch('trabajo-update'); 

            return redirect()->route('trabajos.show', $this->trabajo_id);

        } else {

            $trabajo = Trabajo::create([
                'nombre' => $this->nombre_trabajo,
                'fecha' => now(),
                'vehiculo_cliente_id' => $this->patente,
                'descripcion' => $this->descripcion_trabajo,
            ]);

            foreach ($this->items as $item) {
                DetalleTrabajo::create([
                    'trabajo_id' => $trabajo->id,
                    'articulo_id' => $item['articulo_id'],
                    'cantidad' => $item['cantidad'],
                ]);

                $stock = Stock::where('articulo_id', $item['articulo_id'])->first();

                if ($stock) {
                    $stock->cantidad = max(0, $stock->cantidad - $item['cantidad']);
                    $stock->save();
                }
            }

            $this->dispatch('trabajo-create'); 

        }

        DB::commit();
        $this->reset(['cliente_id', 'patente', 'items', 'codigo_barra', 'cantidad', 'total', 'nombre_trabajo', 'descripcion_trabajo', 'trabajo_id']);
    }

    public function render()
    {
        $this->clientes = Cliente::query()->orderBy('nombre')->get();

        return view('livewire.trabajos.trabajo-create', [
            'clientes' => $this->clientes,
        ]);
    }
}
