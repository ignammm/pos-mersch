<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePresupuesto extends Model
{
    protected $table = 'detalle_presupuestos';

    protected $fillable = [
        'presupuesto_id',
        'articulo_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    /* 🔹 Relaciones */

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}
