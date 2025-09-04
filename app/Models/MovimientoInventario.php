<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'articulo_id',
        'cantidad',
        'tipo',                        
        'motivo',                         // [trabajo, venta]
        'movimiento_origen_id',          // ← Cambiado
        'movimiento_origen_type',        // ← Cambiado
        'estado_reposicion',
        'observaciones',
    ];

    public function movimientoOrigen()
    {
        return $this->morphTo();
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

}
