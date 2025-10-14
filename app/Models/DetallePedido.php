<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'articulo_id',
        'cantidad',
        'pedido_id',
        'detalle_venta_id',
        'repuesto',
    ];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

     public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function detalle_venta()
    {
        return $this->belongsTo(DetalleVenta::class);
    }
}
