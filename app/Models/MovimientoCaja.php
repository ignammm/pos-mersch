<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';
    
    protected $fillable = [
        'caja_id',
        'pago_id',
        'user_id',
        'metodo_pago_id',
        'tipo',
        'monto',
        'descripcion',
        'referencia',
        'fecha_movimiento'
    ];
    
    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_movimiento' => 'datetime'
    ];
    
    const TIPOS = [
        'INGRESO' => 'ingreso',
        'EGRESO' => 'egreso'
    ];
    
    // Relaciones
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
    
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
    
    // Scopes
    public function scopeIngresos($query)
    {
        return $query->where('tipo', self::TIPOS['INGRESO']);
    }
    
    public function scopeEgresos($query)
    {
        return $query->where('tipo', self::TIPOS['EGRESO']);
    }
    
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('fecha_movimiento', $fecha);
    }
    
    public function scopePorMetodoPago($query, $metodoPagoId)
    {
        return $query->where('metodo_pago_id', $metodoPagoId);
    }
    
    public function scopePorCaja($query, $cajaId)
    {
        return $query->where('caja_id', $cajaId);
    }
    
    // Attributes
    public function getEsIngresoAttribute()
    {
        return $this->tipo === self::TIPOS['INGRESO'];
    }
    
    public function getEsEgresoAttribute()
    {
        return $this->tipo === self::TIPOS['EGRESO'];
    }
    
    // Events
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($movimiento) {
            if (empty($movimiento->fecha_movimiento)) {
                $movimiento->fecha_movimiento = now();
            }
            
            if (empty($movimiento->user_id)) {
                $movimiento->user_id = Auth::user()->id;
            }
            
            // Validar que la caja esté abierta
            if ($movimiento->caja && $movimiento->caja->estado !== Caja::ESTADOS['ABIERTA']) {
                throw new \Exception('No se pueden agregar movimientos a una caja cerrada');
            }
        });
        
        static::created(function ($movimiento) {
            // Actualizar timestamp de la caja
            if ($movimiento->caja) {
                $movimiento->caja->touch();
            }
        });
        
        static::deleting(function ($movimiento) {
            // Validar que no se eliminen movimientos de cajas cerradas
            if ($movimiento->caja && $movimiento->caja->estado === Caja::ESTADOS['CERRADA']) {
                throw new \Exception('No se puede eliminar un movimiento de caja cerrada');
            }
        });
    }
}