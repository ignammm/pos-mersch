<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTrabajo extends Model
{
    use HasFactory;

    protected $table = 'detalle_trabajos';

    protected $fillable = [
        'trabajo_id',
        'articulo_id',
        'cantidad',
        'observaciones',
        'activo',
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
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

