<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CuentaCorriente extends Model
{
    use HasFactory;

    protected $table = 'cuentas_corrientes';
    
    protected $fillable = [
        'cliente_id',
        'factura_id',
        'pago_id',
        'tipo_movimiento',
        'monto',
        'saldo_actual',
        'descripcion',
        'fecha_movimiento',
        'fecha_vencimiento',
        'estado'
    ];
    
    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_actual' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
        'fecha_vencimiento' => 'date'
    ];
    
    const TIPOS_MOVIMIENTO = [
        'DEBE' => 'debe',
        'HABER' => 'haber'
    ];
    
    const ESTADOS = [
        'PENDIENTE' => 'pendiente',
        'PAGADO' => 'pagado',
        'VENCIDO' => 'vencido',
        'CANCELADO' => 'cancelado'
    ];
    
    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
    
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
    
    // Scopes
    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }
    
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADOS['PENDIENTE']);
    }
    
    public function scopeVencidos($query)
    {
        return $query->where(function($q) {
            $q->where('estado', self::ESTADOS['VENCIDO'])
              ->orWhere(function($q2) {
                  $q2->where('estado', self::ESTADOS['PENDIENTE'])
                     ->where('fecha_vencimiento', '<', now());
              });
        });
    }
    
    public function scopeDebe($query)
    {
        return $query->where('tipo_movimiento', self::TIPOS_MOVIMIENTO['DEBE']);
    }
    
    public function scopeHaber($query)
    {
        return $query->where('tipo_movimiento', self::TIPOS_MOVIMIENTO['HABER']);
    }
    
    public function scopePorVencer($query, $dias = 7)
    {
        return $query->where('estado', self::ESTADOS['PENDIENTE'])
                    ->where('fecha_vencimiento', '<=', now()->addDays($dias))
                    ->where('fecha_vencimiento', '>', now());
    }
    
    // Attributes
    public function getEsDeudaAttribute()
    {
        return $this->tipo_movimiento === self::TIPOS_MOVIMIENTO['DEBE'];
    }
    
    public function getEsPagoAttribute()
    {
        return $this->tipo_movimiento === self::TIPOS_MOVIMIENTO['HABER'];
    }
    
    public function getEstaVencidoAttribute()
    {
        return $this->fecha_vencimiento && 
               $this->fecha_vencimiento < now() && 
               $this->estado === self::ESTADOS['PENDIENTE'];
    }
    
    // Métodos de negocio
    public function actualizarEstado()
    {
        $nuevoEstado = $this->estado;
        
        if ($this->saldo_actual <= 0 && $this->esDeuda) {
            $nuevoEstado = self::ESTADOS['PAGADO'];
        } elseif ($this->estaVencido) {
            $nuevoEstado = self::ESTADOS['VENCIDO'];
        } else {
            $nuevoEstado = self::ESTADOS['PENDIENTE'];
        }
        
        if ($nuevoEstado !== $this->estado) {
            $this->update(['estado' => $nuevoEstado]);
        }
        
        return $this;
    }
    
    public function marcarComoPagado()
    {
        return $this->update([
            'estado' => self::ESTADOS['PAGADO'],
            'saldo_actual' => 0
        ]);
    }
    
    // Events
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($movimiento) {
            if (empty($movimiento->fecha_movimiento)) {
                $movimiento->fecha_movimiento = now();
            }
            
            // Calcular saldo actual del cliente
            $ultimoMovimiento = self::where('cliente_id', $movimiento->cliente_id)
                ->latest('fecha_movimiento')
                ->first();
                
            $saldoAnterior = $ultimoMovimiento ? $ultimoMovimiento->saldo_actual : 0;
            
            if ($movimiento->tipo_movimiento === self::TIPOS_MOVIMIENTO['DEBE']) {
                $movimiento->saldo_actual = $saldoAnterior + $movimiento->monto;
            } else {
                $movimiento->saldo_actual = $saldoAnterior - $movimiento->monto;
            }
            
            // Si es un pago (haber) y hay deudas, actualizar estados
            if ($movimiento->tipo_movimiento === self::TIPOS_MOVIMIENTO['HABER']) {
                $movimiento->aplicarPagoADeudas();
            }
        });
        
        static::created(function ($movimiento) {
            // Actualizar estado automáticamente
            $movimiento->actualizarEstado();
            
            // Actualizar saldo pendiente en la factura si aplica
            if ($movimiento->factura && $movimiento->esDeuda) {
                $movimiento->factura->update([
                    'saldo_pendiente' => $movimiento->monto
                ]);
            }
        });
    }
    
    private function aplicarPagoADeudas()
    {
        // Aplicar pago a deudas pendientes (método FIFO)
        $deudasPendientes = self::where('cliente_id', $this->cliente_id)
            ->where('tipo_movimiento', self::TIPOS_MOVIMIENTO['DEBE'])
            ->where('estado', self::ESTADOS['PENDIENTE'])
            ->orderBy('fecha_movimiento', 'asc')
            ->get();
            
        $montoRestante = $this->monto;
        
        foreach ($deudasPendientes as $deuda) {
            if ($montoRestante <= 0) break;
            
            if ($deuda->monto <= $montoRestante) {
                // Pagar deuda completa
                $deuda->update([
                    'saldo_actual' => 0,
                    'estado' => self::ESTADOS['PAGADO']
                ]);
                $montoRestante -= $deuda->monto;
            } else {
                // Pagar parte de la deuda
                $deuda->update([
                    'saldo_actual' => $deuda->saldo_actual - $montoRestante
                ]);
                $montoRestante = 0;
            }
            
            // Actualizar saldo pendiente en la factura
            if ($deuda->factura) {
                $deuda->factura->update([
                    'saldo_pendiente' => $deuda->monto
                ]);
            }
        }
    }
}