<?php

namespace App\Livewire\Cajas;

use Livewire\Component;
use App\Services\CajaService;
use Illuminate\Support\Facades\Auth;

class CerrarCaja extends Component
{
    public $montoFinalReal;
    public $observaciones = null;
    public $cajaAbierta;
    public $resumenCaja;

    protected $rules = [
        'montoFinalReal' => 'required|numeric|min:0',
        'observaciones' => 'nullable|string|max:500'
    ];

    public function mount()
    {
        $cajaService = new CajaService();
        $this->cajaAbierta = $cajaService->getCajaAbierta();
        
        if ($this->cajaAbierta) {
            $this->resumenCaja = $cajaService->obtenerResumenCaja($this->cajaAbierta->id);
        }
    }

    public function cerrarCaja()
    {
        $this->validate();

        if (!$this->cajaAbierta) {
            session()->flash('error', 'No hay caja abierta para cerrar');
            return;
        }

        $cajaService = new CajaService();
        
        try {
            $cajaService->cerrarCaja(
                $this->cajaAbierta->id,
                $this->montoFinalReal,
                $this->observaciones
            );
            
            // Actualizar el estado
            $this->cajaAbierta = null;
            $this->resumenCaja = null;
            
            // Limpiar campos
            $this->reset(['montoFinalReal', 'observaciones']);
            
            session()->flash('success', 'Caja cerrada correctamente');
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function calcularDiferencia()
    {
        if (!$this->resumenCaja) return 0;
        
        $montoEsperado = $this->resumenCaja['saldo_actual'];
        
        // Si está vacío o no es numérico, usar 0
        if (empty($this->montoFinalReal) || !is_numeric($this->montoFinalReal)) {
            return 0 - $montoEsperado;
        }
        
        return floatval($this->montoFinalReal) - $montoEsperado;
}

    public function updatedMontoFinalReal()
    {
        // Recalcular diferencia cuando cambia el monto
        $this->calcularDiferencia();
    }

    public function render()
    {
        return view('livewire.cajas.cerrar-caja');
    }
}