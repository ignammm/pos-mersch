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
        'numero',
        'monto_original',
        'monto_final',
        'descuento_aplicado',
        'forma_pago',
        'estado',
    ];


    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public static function numeroComprobante($tipo_comprobante)
    {
        // Obtenemos la última factura del tipo deseado
        $ultimaFactura = Factura::where('tipo_comprobante', $tipo_comprobante)
            ->orderByDesc('numero')
            ->first();

        $siguienteNro = $ultimaFactura ? $ultimaFactura->numero + 1 : 1;

        return $siguienteNro;
    }
}
