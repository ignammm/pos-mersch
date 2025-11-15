<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'numero_comprobante',
        'fecha',
        'total',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleIngresos::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
