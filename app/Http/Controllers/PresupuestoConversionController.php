<?php

// app/Http/Controllers/PresupuestoConversionController.php
namespace App\Http\Controllers;

use App\Http\Requests\ConvertirPresupuestoRequest;
use App\Services\PresupuestoConversionService;
use App\DTO\ConvertirPresupuestoDTO;
use App\Models\Presupuesto;

class PresupuestoConversionController extends Controller
{
    public function __construct(private PresupuestoConversionService $conversionService) {}
    
    public function store($request, Presupuesto $presupuesto)
    {
        try {
            $dto = ConvertirPresupuestoDTO::fromRequest($request);
            $resultado = $this->conversionService->convertir($presupuesto, $dto);
            
            return $this->redireccionarSegunTipo($dto->tipo, $resultado);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al convertir: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    private function redireccionarSegunTipo(string $tipo, $model)
    {
        $rutas = [
            'venta' => 'ventas.show',
            'trabajo' => 'trabajos.show'
        ];
        
        return redirect()->route($rutas[$tipo], $model->id)
            ->with('success', "Presupuesto convertido a " . ucfirst($tipo) . " exitosamente");
    }
}