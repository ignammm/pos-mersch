<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';
    
    protected $fillable = [
        'user_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_final_esperado',
        'monto_final_real',
        'diferencia',
        'estado',
        'observaciones'
    ];
    
    protected $casts = [
        'monto_inicial' => 'decimal:2',
        'monto_final_esperado' => 'decimal:2',
        'monto_final_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime'
    ];
    
    const ESTADOS = [
        'ABIERTA' => 'abierta',
        'CERRADA' => 'cerrada',
        'BLOQUEADA' => 'bloqueada'
    ];
    
    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }
    
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
    
    // Scopes
    public function scopeAbierta($query)
    {
        return $query->where('estado', self::ESTADOS['ABIERTA']);
    }
    
    public function scopeCerrada($query)
    {
        return $query->where('estado', self::ESTADOS['CERRADA']);
    }
    
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('fecha_apertura', $fecha);
    }
    
    // Métodos de negocio
    public function calcularTotalIngresos()
    {
        return $this->movimientos()->where('tipo', 'ingreso')->sum('monto');
    }
    
    public function calcularTotalEgresos()
    {
        return $this->movimientos()->where('tipo', 'egreso')->sum('monto');
    }
    
    public function calcularSaldoTeorico()
    {
        return $this->monto_inicial + 
               $this->calcularTotalIngresos() - 
               $this->calcularTotalEgresos();
    }
    
    public function getSaldoActualAttribute()
    {
        return $this->calcularSaldoTeorico();
    }
    
    public function cerrar($montoFinalReal, $observaciones = null)
    {
        $saldoTeorico = $this->calcularSaldoTeorico();
        
        $this->update([
            'fecha_cierre' => now(),
            'monto_final_esperado' => $saldoTeorico,
            'monto_final_real' => $montoFinalReal,
            'diferencia' => $montoFinalReal - $saldoTeorico,
            'estado' => self::ESTADOS['CERRADA'],
            'observaciones' => $observaciones
        ]);
        
        return $this;
    }
    
    public function abrir($montoInicial, $observaciones = null)
    {
        $this->update([
            'fecha_apertura' => now(),
            'monto_inicial' => $montoInicial,
            'estado' => self::ESTADOS['ABIERTA'],
            'observaciones' => $observaciones
        ]);
        
        return $this;
    }
    
    public function puedeCerrarse()
    {
        return $this->estado === self::ESTADOS['ABIERTA'];
    }
    
    public function registrarIngreso($monto, $descripcion, $metodoPagoId, $pagoId = null)
    {
        return $this->movimientos()->create([
            'pago_id' => $pagoId,
            'user_id' => Auth::user()->id ?? $this->user_id,
            'metodo_pago_id' => $metodoPagoId,
            'tipo' => 'ingreso',
            'monto' => $monto,
            'descripcion' => $descripcion,
            'fecha_movimiento' => now()
        ]);
    }
    
    public function registrarEgreso($monto, $descripcion, $referencia = null)
    {
        return $this->movimientos()->create([
            'user_id' => Auth::user()->id ?? $this->user_id,
            'tipo' => 'egreso',
            'monto' => $monto,
            'descripcion' => $descripcion,
            'referencia' => $referencia,
            'fecha_movimiento' => now()
        ]);
    }
}