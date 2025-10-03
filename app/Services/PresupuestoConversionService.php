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
        if($this->articuloSuperanStock($presupuesto))
        {
            return $this->articuloSuperanStock($presupuesto);
        }

        DB::transaction(function() use ($presupuesto, $dto) {
            // $this->validarConversion($presupuesto);
            
            match($dto->tipo) {
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
        $this->darSalidaArticulos($venta->detalles, 'venta');
        $this->marcarPresupuestoConvertido($presupuesto, Factura::class, $venta->id);
        
        // Evento opcional para notificaciones
        // event(new PresupuestoConvertido($presupuesto, $venta));
        
        return $venta;
    }
    
    private function convertirATrabajo(Presupuesto $presupuesto, ConvertirPresupuestoDTO $dto): Trabajo
    {
        $trabajo = new Trabajo();
        $trabajo->fill([
            'nombre' => $dto->nombre_trabajo,
            'fecha' => now(),
            'vehiculo_cliente_id' => $dto->vehiculo_cliente_id,
            'presupuesto_id' => $presupuesto->id,
            'descripcion' => $dto->descripcion_trabajo,
            'estado' => 'pendiente'
        ]);
        
        $trabajo->save();
        $this->copiarDetallesATrabajo($presupuesto, $trabajo);
        $this->darSalidaArticulos($trabajo->detalles, 'trabajo');
        $this->marcarPresupuestoConvertido($presupuesto, Trabajo::class , $trabajo->id);
        
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
                'trabajo_id' => $trabajo->id,
                'articulo_id' => $detalle->articulo_id,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
            ]);
        }
    }

    private function darSalidaArticulos($detalles, $tipo): void
    {
        foreach ($detalles as $detalle) {
           
            $detalle->movimientos()->create([
                'articulo_id' => $detalle->articulo_id,
                'cantidad' => $detalle->cantidad,
                'tipo' => 'salida',
                'estado_reposicion' => 'pendiente',
                'motivo' => $tipo,
                'observaciones' => 'Transformacion de un presupuesto a tipo '. $tipo . '.',
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

    private function articuloSuperanStock(Presupuesto $presupuesto): array
    {
        $articulos = [];

        foreach ($presupuesto->detalles as $detalle) {

            if ($detalle->articulo->stock->cantidad < $detalle->cantidad) {
                $articulos[] = $detalle->articulo->articulo;
            }
        }

        return $articulos;
    }
}