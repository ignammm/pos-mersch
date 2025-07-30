<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenciaRsf extends Model
{
    use HasFactory;

    protected $table = 'referencia_rsf';

    protected $fillable = [
        'marca_rsf',
        'articulo',
        'fabrica',
        'descripcion',
        'tipo_txt',
        'marca_original',
        'precio_lista',
        'precio_neto',
        'stock_final',
        'modulo_venta',
        'rubro',
        'segmento',
        'enlace',
        'enlace',
        'codigo_barra',
        'codigo_rsf',
    ];
}
