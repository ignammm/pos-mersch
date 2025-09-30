<?php

// app/Services/PresupuestoConversionService.php
namespace App\Services;

use App\DTO\ConvertirPresupuestoDTO;
use App\Models\Factura;
use App\Models\Presupuesto;
use App\Models\Venta;
use App\Models\Trabajo;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class PresupuestoConversionService
{
    public function convertir(Presupuesto $presupuesto, ConvertirPresupuestoDTO $dto)
    {
        return DB::transaction(function() use ($presupuesto, $dto) {
            // $this->validarConversion($presupuesto);
            
            return match($dto->tipo) {
                'venta' => $this->convertirAVenta($presupuesto, $dto),
                'trabajo' => $this->convertirATrabajo($presupuesto, $dto),
                default => throw new Exception('Tipo de conversión no válido')
            };
        });
    }
    
    private function validarConversion(Presupuesto $presupuesto): void
    {
        if ($presupuesto->estado !== 'aprobado') {
            throw new Exception('Solo se pueden convertir presupuestos aprobados');
        }
        
        if ($presupuesto->convertido_a) {
            throw new Exception('Este presupuesto ya fue convertido');
        }
    }
    
    private function convertirAVenta(Presupuesto $presupuesto, ConvertirPresupuestoDTO $dto): Factura
    {
        $venta = new Factura();
        $venta->fill([
            'cliente_id' => $dto->cliente_id,
            'user_id' => Auth::user()->id,
            'presupuesto_id' => $presupuesto->id,
            'monto_original' => $presupuesto->subtotal,
            'estado' => 'pendiente',
            'fecha' => now(),
            'tipo_comprobante' => 'Ticket',
            'numero' => Factura::numeroComprobante('Ticket'),
        ]);
        
        $venta->save();
        $this->copiarDetallesAVenta($presupuesto, $venta);
        $this->marcarPresupuestoConvertido($presupuesto, Factura::class, $venta->id);
        
        // Evento opcional para notificaciones
        // event(new PresupuestoConvertido($presupuesto, $venta));
        
        return $venta;
    }
    
    private function convertirATrabajo(Presupuesto $presupuesto, ConvertirPresupuestoDTO $dto): Trabajo
    {
        $trabajo = new Trabajo();
        $trabajo->fill([
            // 'presupuesto_id' => $presupuesto->id,
            // 'cliente_id' => $presupuesto->cliente_id,
            // 'fecha_entrega_estimada' => $dto->fecha_entrega,
            // 'prioridad' => 'media',
            // 'estado' => 'programado',
            // 'observaciones' => $dto->observaciones,
            // 'datos_tecnicos' => $dto->datos_adicionales
        ]);
        
        $trabajo->save();
        $this->copiarDetallesATrabajo($presupuesto, $trabajo);
        $this->marcarPresupuestoConvertido($presupuesto, 'trabajo', $trabajo->id);
        
        return $trabajo;
    }
    
    private function copiarDetallesAVenta(Presupuesto $presupuesto, Factura $venta): void
    {
        foreach ($presupuesto->detalles as $detalle) {
            $venta->detalles()->create([
                'factura_id' => $venta->id,
                'articulo_id' => $detalle->articulo_id,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal' => $detalle->subtotal
            ]);
        }
    }

    private function copiarDetallesATrabajo(Presupuesto $presupuesto, Trabajo $trabajo): void
    {
        foreach ($presupuesto->detalles as $detalle) {
            $trabajo->detalles()->create([
                'producto_id' => $detalle->producto_id,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal' => $detalle->subtotal
            ]);
        }
    }
    
    private function marcarPresupuestoConvertido(Presupuesto $presupuesto, $tipo, int $idConvertido): void
    {
        $presupuesto->update([
            'estado' => 'aceptado',
            'tipo_conversion' => $tipo,
            'conversion_id' => $idConvertido,
        ]);
    }
}