<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleIngresos extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingreso_id',
        'articulo_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];
}
