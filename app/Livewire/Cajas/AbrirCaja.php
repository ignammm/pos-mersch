<?php

namespace App\Livewire\Cajas;

use Livewire\Component;
use App\Services\CajaService;
use Illuminate\Support\Facades\Auth;

class AbrirCaja extends Component
{
    public $montoInicial, $observaciones = null, $cajaAbierta;

    public function mount()
    {
        $cajaService = new CajaService();
        $this->cajaAbierta = $cajaService->getCajaAbierta();
    }

    public function abrirCaja()
    {
        $this->validate([
            'montoInicial' => 'required|numeric|min:0',
        ]);

        $cajaService = new CajaService();
        
        try {
            $cajaService->abrirCaja($this->montoInicial, Auth::user()->id, $this->observaciones);
            
            // Actualizar el estado después de abrir
            $this->cajaAbierta = $cajaService->getCajaAbierta();
            
            // Limpiar campos
            $this->montoInicial = null;
            $this->observaciones = null;
            
            session()->flash('success', 'Caja abierta correctamente');
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.cajas.abrir-caja');
    }
}