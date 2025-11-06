<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    
    protected $fillable = [
        'factura_id',
        'metodo_pago_id',
        'caja_id',
        'user_id',
        'monto',
        'comision',
        'neto',
        'estado',
        'fecha_pago',
        'fecha_confirmacion',
        'fecha_liquidacion',
        'referencia',
        'codigo_autorizacion',
        'comprobante',
        'datos_tarjeta',
        'entidad_bancaria',
        'numero_cheque',
        'fecha_vencimiento_cheque',
        'observaciones',
        'notas_internas'
    ];
    
    protected $casts = [
        'monto' => 'decimal:2',
        'comision' => 'decimal:2',
        'neto' => 'decimal:2',
        'fecha_pago' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'fecha_liquidacion' => 'datetime',
        'fecha_vencimiento_cheque' => 'date',
        'datos_tarjeta' => 'array'
    ];
    
    const ESTADOS = [
        'PENDIENTE' => 'pendiente',
        'COMPLETADO' => 'completado',
        'FALLIDO' => 'fallido',
        'REVERSADO' => 'reversado',
        'REEMBOLSADO' => 'reembolsado'
    ];
    
    // Relaciones
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
    
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
    
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function movimientoCaja()
    {
        return $this->hasOne(MovimientoCaja::class);
    }
    
    public function cuentaCorriente()
    {
        return $this->hasOne(CuentaCorriente::class);
    }
    
    // Scopes
    public function scopeCompletados($query)
    {
        return $query->where('estado', self::ESTADOS['COMPLETADO']);
    }
    
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADOS['PENDIENTE']);
    }
    
    public function scopeFallidos($query)
    {
        return $query->where('estado', self::ESTADOS['FALLIDO']);
    }
    
    public function scopeReversados($query)
    {
        return $query->where('estado', self::ESTADOS['REVERSADO']);
    }
    
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('fecha_pago', $fecha);
    }
    
    public function scopePorMetodo($query, $metodoPagoId)
    {
        return $query->where('metodo_pago_id', $metodoPagoId);
    }
    
    public function scopePorRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_pago', [$desde, $hasta]);
    }
    
    public function scopeConConfirmacion($query)
    {
        return $query->whereNotNull('fecha_confirmacion');
    }
    
    // Métodos de negocio
    public function calcularComision()
    {
        if ($this->metodoPago) {
            return $this->metodoPago->calcularComision($this->monto);
        }
        return 0;
    }
    
    public function puedeReversarse()
    {
        return $this->estado === self::ESTADOS['COMPLETADO'] &&
               $this->fecha_pago->gt(now()->subHours(24)) &&
               in_array($this->metodoPago->tipo, ['tarjeta_credito', 'tarjeta_debito']);
    }
    
    public function marcarComoCompletado($datosAdicionales = [])
    {
        $comision = $this->calcularComision();
        
        $this->update(array_merge([
            'estado' => self::ESTADOS['COMPLETADO'],
            'fecha_confirmacion' => now(),
            'comision' => $comision,
            'neto' => $this->monto - $comision
        ], $datosAdicionales));
        
        // Registrar en caja si es método que va a caja
        if ($this->metodoPago->requiereCaja() && $this->caja_id) {
            $this->registrarEnCaja();
        }
        
        return true;
    }
    
    public function registrarEnCaja()
    {
        if ($this->caja_id && !$this->movimientoCaja) {
            return MovimientoCaja::create([
                'caja_id' => $this->caja_id,
                'pago_id' => $this->id,
                'user_id' => $this->user_id,
                'metodo_pago_id' => $this->metodo_pago_id,
                'tipo' => 'ingreso',
                'monto' => $this->monto,
                'descripcion' => 'Pago de factura #' . $this->factura_id,
                'fecha_movimiento' => $this->fecha_pago
            ]);
        }
    }
    
    public function reversar($motivo)
    {
        if (!$this->puedeReversarse()) {
            throw new \Exception('No se puede reversar este pago');
        }
        
        $this->update([
            'estado' => self::ESTADOS['REVERSADO'],
            'observaciones' => $this->observaciones . " | Reversado: {$motivo}"
        ]);
        
        // Revertir movimiento en caja si existe
        if ($this->movimientoCaja) {
            $this->movimientoCaja->delete();
        }
        
        return true;
    }
    
    // Events
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pago) {
            // Asignar fecha de pago si no existe
            if (empty($pago->fecha_pago)) {
                $pago->fecha_pago = now();
            }
            
            // Asignar usuario si no existe
            // if (empty($pago->user_id) && auth()->check()) {
            //     $pago->user_id = auth()->id();
            // }
            
            // Calcular comisión y neto si no existen
            if (empty($pago->comision)) {
                $pago->comision = $pago->calcularComision();
            }
            
            if (empty($pago->neto)) {
                $pago->neto = $pago->monto - $pago->comision;
            }
        });
    }
    
    private function registrarEnCuentaCorriente()
    {
        if ($this->factura && $this->factura->cliente) {
            return CuentaCorriente::create([
                'cliente_id' => $this->factura->cliente_id,
                'pago_id' => $this->id,
                'factura_id' => $this->factura_id,
                'tipo_movimiento' => 'haber',
                'monto' => $this->monto,
                'descripcion' => 'Pago de factura #' . $this->factura_id,
                'fecha_movimiento' => $this->fecha_pago
            ]);
        }
    }
}