<?php

namespace App\Livewire\Pagos;

use Livewire\Component;
use App\Models\Factura;
use App\Models\MetodoPago;
use App\Services\PagoService;

class ProcesarPago extends Component
{
    public $facturaId;
    public $metodoPagoId;
    public $monto;
    public $referencia;
    public $codigoAutorizacion;
    
    public $factura;
    public $metodosPago = [];

    protected $rules = [
        'facturaId' => 'required|exists:facturas,id',
        'metodoPagoId' => 'required|exists:metodos_pago,id',
        'monto' => 'required|numeric|min:0.01',
        'referencia' => 'nullable|string|max:255',
        'codigoAutorizacion' => 'nullable|string|max:255'
    ];

    public function mount($facturaId = null)
    {
        if ($facturaId) {
            $this->facturaId = $facturaId;
            $this->factura = Factura::find($facturaId);
            $this->monto = $this->factura->total ?? 0;
        }

        $this->metodosPago = MetodoPago::activos()->paraFacturas()->get();
    }

    public function procesarPago()
    {
        $this->validate();

        try {
            $pagoService = new PagoService();
            $pago = $pagoService->procesarPago(
                $this->facturaId,
                $this->metodoPagoId,
                $this->monto,
                [
                    'referencia' => $this->referencia,
                    'codigo_autorizacion' => $this->codigoAutorizacion
                ]
            );

            session()->flash('message', 'Pago procesado correctamente');
            $this->resetForm();

            // Emitir evento para actualizar otras componentes
            $this->emit('pagoProcesado', $pago->id);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset(['metodoPagoId', 'monto', 'referencia', 'codigoAutorizacion']);
    }

    public function render()
    {
        return view('livewire.pagos.procesar-pago');
    }
}