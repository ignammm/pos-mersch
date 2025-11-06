<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha',
        'tipo_comprobante',
        'trabajo_id',
        'presupuesto_id',
        'numero',
        'monto_original',
        'saldo_pendiente',
        'monto_final',
        'descuento_aplicado',
        'forma_pago',
        'estado',
    ];

    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_PARCIAL = 'parcial';
    const ESTADO_PAGADA = 'pagada';
    const ESTADO_MOROSO = 'moroso';

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    // Relación con pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    // Método para calcular saldo pendiente
    public function calcularSaldoPendiente()
    {
        $totalPagado = $this->pagos()->sum('monto');
        return $this->monto_original - $totalPagado;
    }

    // Método para actualizar estado
    public function actualizarEstadoPago()
    { 
        if ($this->saldo_pendiente <= 0) {
            $this->estado = self::ESTADO_PAGADA;
        } elseif ($this->saldo_pendiente < $this->monto_original) {
            $this->estado = self::ESTADO_PARCIAL;
        } else {
            $this->estado = self::ESTADO_PENDIENTE;
        }
        
        $this->save();
    }

    public static function numeroComprobante($tipo_comprobante)
    {
        $ultimaFactura = Factura::where('tipo_comprobante', $tipo_comprobante)
            ->orderByDesc('numero')
            ->first();

        if ($ultimaFactura && is_numeric($ultimaFactura->numero)) {
            return (int) $ultimaFactura->numero + 1;
        }

        return 1;
    }

    public function presupuesto()
    {
        return $this->morphOne(Presupuesto::class, 'conversion', 'tipo_conversion', 'conversion_id');
    }
}
