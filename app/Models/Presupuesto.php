<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $fillable = [
        'numero',
        'cliente_id',
        'usuario_id',
        'fecha_emision',
        'fecha_validez',
        'estado',
        'subtotal',
        'total_estimado',
        'observaciones',
        'tipo_conversion',
        'conversion_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetallePresupuesto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function conversion()
    {
        return $this->morphTo(__FUNCTION__, 'tipo_conversion', 'conversion_id');
    }

    public static function generarNumero()
    {
        $ultimo = Presupuesto::orderBy('numero', 'desc')->first();

        if ($ultimo) {
            $numeroInt = intval(substr($ultimo->numero, 2)); // saca el número
            $proximoNumero = 'P-' . str_pad($numeroInt + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $proximoNumero = 'P-000001';
        }

        return $proximoNumero;
    }
}
