<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\error;

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


    public static function getByCodigo($codigo_barra)
    {
        return ReferenciaRsf::where('codigo_rsf', $codigo_barra)
            ->orWhere('codigo_barra', $codigo_barra)
            ->orWhere('articulo', $codigo_barra);
    }

    public static function existsReferenciaRsf($codigo_barra)
    {
        return ReferenciaRsf::where('codigo_rsf', $codigo_barra)
            ->orWhere('codigo_barra', $codigo_barra)
            ->orWhere('articulo', $codigo_barra)
            ->exists();
    }
    
}
