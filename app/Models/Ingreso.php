<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'tipo_comprobante',
        'numero_comprobante',
        'fecha',
        'total',
    ];
}
