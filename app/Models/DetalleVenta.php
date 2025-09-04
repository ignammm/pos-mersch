<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'articulo_id',
        'cantidad',
        'precio_unitario',
        'precio_descuento',
        'descuento_aplicado',
        'subtotal',
        'repuesto',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function movimientos()
    {
        return $this->morphMany(MovimientoInventario::class, 'movimiento_origen');
    }

}
