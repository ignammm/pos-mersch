<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo_interno',
        'codigo_proveedor',
        'rubro',
        'precio',
        'marca',
        'descripcion',
        'unidad',
        'proveedor_id',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public static function generarCodigoInterno(): string
    {
        $ultimo = Articulo::max('id') ?? 0;
        $nuevo = $ultimo + 1;
        return 'RM' . str_pad($nuevo, 6, '0', STR_PAD_LEFT); // Ej: RM000123
    }



}
