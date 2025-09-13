<?php

namespace App\Livewire\Presupuestos;

use Livewire\Component;
use App\Models\Presupuesto;
use App\Models\Factura;
use App\Models\Trabajo;

class PresupuestoShow extends Component
{
    public Presupuesto $presupuesto;
    public $detalles_presupuesto, $total;

    protected $listeners = ['presupuesto-update' => '$refresh'];

    public function mount(Presupuesto $id)
    {
        $this->presupuesto = $id;
        $this->detalles_presupuesto = $this->presupuesto->detalles()->with('articulo')->get();
        $this->total = collect($this->detalles_presupuesto)->sum('subtotal');
    }

    public function eliminarPresupuesto()
    {
        $this->presupuesto->activo = false;
        $this->presupuesto->save();
        $this->presupuesto->detalles()->update(['activo' => false]);
        session()->flash('message', 'Presupuesto eliminado correctamente.');
        return redirect()->route('presupuestos.index');
    }

    public function presupuestoVenta()
    {
        // 1) Crear la venta a partir del presupuesto
        $venta = Factura::create([
            'cliente_id'   => $this->presupuesto->cliente_id,
            'fecha'        => now(),
            'total'        => $this->presupuesto->total_estimado,
            'observaciones'=> $this->presupuesto->observaciones,
        ]);

        foreach ($this->detalles_presupuesto as $dp) {
            $venta->detalles()->create([
                'articulo_id'    => $dp->articulo_id,
                'descripcion'    => $dp->descripcion,
                'cantidad'       => $dp->cantidad,
                'precio_unitario'=> $dp->precio_unitario,
                'subtotal'       => $dp->subtotal,
            ]);
        }

        // 2) Marcar el presupuesto como convertido
        $this->presupuesto->update([
            'estado'         => 'convertido',
            'tipo_conversion'=> Factura::class,
            'conversion_id'  => $venta->id,
        ]);

        $this->dispatch('presupuesto-update');
    }

    public function presupuestoTrabajo()
    {
        // 1) Crear el trabajo a partir del presupuesto
        $trabajo = Trabajo::create([
            'cliente_id'   => $this->presupuesto->cliente_id,
            'fecha'        => now(),
            'estado'       => 'pendiente',
            'observaciones'=> $this->presupuesto->observaciones,
        ]);

        foreach ($this->detalles_presupuesto as $dp) {
            $trabajo->detalles()->create([
                'articulo_id'    => $dp->articulo_id,
                'descripcion'    => $dp->descripcion,
                'cantidad'       => $dp->cantidad,
                'precio_unitario'=> $dp->precio_unitario,
                'subtotal'       => $dp->subtotal,
            ]);
        }

        // 2) Marcar el presupuesto como convertido
        $this->presupuesto->update([
            'estado'         => 'convertido',
            'tipo_conversion'=> Trabajo::class,
            'conversion_id'  => $trabajo->id,
        ]);

        $this->dispatch('presupuesto-update');
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
