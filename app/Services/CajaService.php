<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaService
{
    public function abrirCaja($montoInicial, $userId, $observaciones = null)
    {
        // Verificar si ya hay una caja abierta
        if ($this->hayCajaAbierta()) {
            return true;
        }

        return Caja::create([
            'user_id' => $userId,
            'fecha_apertura' => now(),
            'monto_inicial' => $montoInicial,
            'estado' => Caja::ESTADOS['ABIERTA'],
            'observaciones' => $observaciones
        ]);
    }

    public function cerrarCaja($cajaId, $montoFinalReal, $observaciones = null)
    {
        return DB::transaction(function () use ($cajaId, $montoFinalReal, $observaciones) {
            $caja = Caja::findOrFail($cajaId);
            
            if ($caja->estado === Caja::ESTADOS['CERRADA']) {
                throw new \Exception('La caja ya está cerrada');
            }

            $montoFinalEsperado = $this->calcularMontoFinalEsperado($caja);
            $diferencia = $montoFinalReal - $montoFinalEsperado;

            $caja->update([
                'fecha_cierre' => now(),
                'monto_final_esperado' => $montoFinalEsperado,
                'monto_final_real' => $montoFinalReal,
                'diferencia' => $diferencia,
                'estado' => Caja::ESTADOS['CERRADA'],
                'observaciones' => $observaciones
            ]);

            return $caja;
        });
    }

    public function hayCajaAbierta()
    {
        return Caja::where('estado', Caja::ESTADOS['ABIERTA'])->exists();
    }

    public function getCajaAbierta()
    {
        return Caja::with('user')
            ->where('estado', Caja::ESTADOS['ABIERTA'])
            ->first();
    }

    public function calcularMontoFinalEsperado(Caja $caja)
    {
        $totalIngresos = $caja->movimientos()
            ->where('tipo', MovimientoCaja::TIPOS['INGRESO'])
            ->sum('monto');

        $totalEgresos = $caja->movimientos()
            ->where('tipo', MovimientoCaja::TIPOS['EGRESO'])
            ->sum('monto');

        return $caja->monto_inicial + $totalIngresos - $totalEgresos;
    }

    public function registrarIngreso($cajaId, $monto, $descripcion, $metodoPagoId = null, $ventaId = null)
    {
        return MovimientoCaja::create([
            'caja_id' => $cajaId,
            'user_id' => Auth::user()->id,
            'tipo' => MovimientoCaja::TIPOS['INGRESO'],
            'monto' => $monto,
            'descripcion' => $descripcion,
            'metodo_pago_id' => $metodoPagoId,
            'venta_id' => $ventaId,
            'es_gasto' => false
        ]);
    }

    public function registrarEgreso($cajaId, $monto, $descripcion, $esGasto = false)
    {
        return MovimientoCaja::create([
            'caja_id' => $cajaId,
            'user_id' => Auth::user()->id,
            'tipo' => MovimientoCaja::TIPOS['EGRESO'],
            'monto' => $monto,
            'descripcion' => $descripcion,
            'es_gasto' => $esGasto
        ]);
    }

    public function obtenerResumenCaja($cajaId)
    {
        $caja = Caja::with(['movimientos', 'movimientos.metodoPago'])->find($cajaId);
        
        $ingresosPorMetodo = $caja->movimientos
            ->where('tipo', MovimientoCaja::TIPOS['INGRESO'])
            ->groupBy('metodo_pago_id')
            ->map(function ($movimientos) {
                return $movimientos->sum('monto');
            });

        $totalIngresos = $caja->movimientos
            ->where('tipo', MovimientoCaja::TIPOS['INGRESO'])
            ->sum('monto');

        $totalEgresos = $caja->movimientos
            ->where('tipo', MovimientoCaja::TIPOS['EGRESO'])
            ->sum('monto');

        $totalGastos = $caja->movimientos
            ->where('es_gasto', true)
            ->sum('monto');

        return [
            'caja' => $caja,
            'ingresos_por_metodo' => $ingresosPorMetodo,
            'total_ingresos' => $totalIngresos,
            'total_egresos' => $totalEgresos,
            'total_gastos' => $totalGastos,
            'saldo_actual' => $caja->monto_inicial + $totalIngresos - $totalEgresos
        ];
    }
}