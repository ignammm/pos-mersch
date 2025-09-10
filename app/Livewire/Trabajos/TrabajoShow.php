<?php

namespace App\Livewire\Trabajos;

use App\Models\Articulo;
use App\Models\DetalleTrabajo;
use App\Models\DetalleVenta;
use App\Models\Factura;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TrabajoShow extends Component
{

    public Trabajo $trabajo;

    public function mount($id)
    {
        $this->trabajo = Trabajo::findOrFail($id);
    }

    public function eliminarTrabajo()
    {
        $this->trabajo->detalles()->update(['activo' => false]);

        $this->trabajo->update([
            'estado' => 'cancelado',
        ]);

        session()->flash('message', 'Trabajo eliminado correctamente');
        
        return redirect()->route('trabajos.index');
    }

    public function finalizarTrabajo()
    {
        $this->trabajo->update([
            'estado' => 'finalizado',
        ]);

        $this->generarVenta();

        session()->flash('message', 'Su trabajo se acaba de finalizar correctamente');
        
        return redirect()->route('trabajos.index');
    }

    public function generarVenta()
    {
        $user = Auth::user();
        $venta = Factura::create([
            'cliente_id' => $this->trabajo->vehiculoCliente->cliente->id,
            'user_id' => $user->id,
            'trabajo_id' => $this->trabajo->id,
            'fecha' => now(),
            'tipo_comprobante' => 'Ticket',
            'numero' => Factura::numeroComprobante('Ticket'),
        ]);
        foreach ($this->trabajo->detallesActivos as $detalle) {
            DetalleVenta::create([
                'factura_id' => $venta->id,
                'articulo_id' => $detalle->articulo_id,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => Articulo::find($detalle->articulo_id)->precio,
                'subtotal' => $detalle->cantidad * Articulo::find($detalle->articulo_id)->precio
            ]);
        }
    }


    public function render()
    {
        $detalles_trabajo = DetalleTrabajo::with('articulo')
        ->where('trabajo_id', $this->trabajo->id)
        ->where('activo', true)
        ->get();
        return view('livewire.trabajos.trabajo-show', [
            'detalles_trabajo' => $detalles_trabajo,
        ]);
    }
}
