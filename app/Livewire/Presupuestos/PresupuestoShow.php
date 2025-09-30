<?php

namespace App\Livewire\Presupuestos;

use App\DTO\ConvertirPresupuestoDTO;
use App\Http\Controllers\PresupuestoConversionController;
use App\Models\Cliente;
use Livewire\Component;
use App\Models\Presupuesto;
use App\Models\Factura;
use App\Models\Trabajo;
use App\Models\VehiculoCliente;
use App\Services\PresupuestoConversionService;

class PresupuestoShow extends Component
{
    public Presupuesto $presupuesto;
    public $detalles_presupuesto, $total, $mostrarConvertirPresupuesto = false,
    $clientes, $cliente_id, $vehiculos_cliente = [], $vehiculo_cliente_id, $tipoConversion;
    public $presupuesto_id;
    public $tipo_conversion = '';
    public $nombre_trabajo = '';
    public $descripcion_trabajo = '';


    protected $listeners = ['presupuesto-update' => '$refresh'];

    public function mount(Presupuesto $id)
    {
        $this->presupuesto = $id;
        $this->detalles_presupuesto = $this->presupuesto->detalles()->with('articulo')->get();
        $this->total = collect($this->detalles_presupuesto)->sum('subtotal');
        $this->clientes = Cliente::all();
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
            $this->vehiculos_cliente = [];
            $this->addError('cliente', 'Este cliente no tiene ningun vehiculo a su nombre, asignele uno.');
            return;
        }
    }

    public function eliminarPresupuesto()
    {
        $this->presupuesto->activo = false;
        $this->presupuesto->save();
        $this->presupuesto->detalles()->update(['activo' => false]);
        session()->flash('message', 'Presupuesto eliminado correctamente.');
        return redirect()->route('presupuestos.index');
    }

    public function abrirModalConvertidorPresupuesto()
    {
        $this->mostrarConvertirPresupuesto = true;
    }


    public function presupuestoConfirmar()
    {
        $this->validate([
            'tipo_conversion' => 'required|in:venta,trabajo',
            'cliente_id' => 'required',
            'vehiculo_cliente_id' => 'required_if:tipo_conversion,trabajo',
            'nombre_trabajo' => 'required_if:tipo_conversion,trabajo',
            'descripcion_trabajo' => 'nullable',
        ]);
        
        try {
            // Obtener el presupuesto actual (ajusta según tu lógica)
            $presupuesto = Presupuesto::find($this->presupuesto->id);
            
            // Crear DTO con los datos
            $dto = new ConvertirPresupuestoDTO(
                tipo: $this->tipo_conversion,
                cliente_id: $this->cliente_id,
                vehiculo_cliente_id: $this->vehiculos_cliente_id ?? null,
                nombre_trabajo: $this->nombre_trabajo ?? null,
                descricion_trabajo: $this->descripcion_trabajo ?? null,
            );
            
            // Llamar al service
            $conversionService = app(PresupuestoConversionService::class);
            $resultado = $conversionService->convertir($presupuesto, $dto);
            
            // Redireccionar o mostrar mensaje
            session()->flash('success', 'Presupuesto convertido exitosamente');
            
            // Si estás en Livewire, puedes emitir evento
            // $this->emit('presupuestoConvertido', $resultado->id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function presupuestoRechazar()
    {
        $this->presupuesto->estado = 'rechazado';
        $this->presupuesto->save();
        session()->flash('message', 'Presupuesto eliminado correctamente.');
        return redirect()->route('presupuestos.index');
    }

    public function render()
    {
        return view('livewire.presupuestos.presupuesto-show');
    }
}
