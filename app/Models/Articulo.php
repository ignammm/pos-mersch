<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'articulo',
        'codigo_interno',
        'codigo_proveedor',
        'codigo_fabricante',
        'rubro',
        'precio',
        'marca',
        'descripcion',
        'enlace',
        'unidad',
        'proveedor_id',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }


    public static function generarCodigoInterno(): string
    {
        $ultimo = Articulo::max('id') ?? 0;
        $nuevo = $ultimo + 1;
        return 'RM' . str_pad($nuevo, 6, '0', STR_PAD_LEFT); // Ej: RM000123
    }

    public function equivalentes()
    {
        $referencia = ReferenciaRsf::where('codigo_rsf', $this->codigo_proveedor)->first();

        $equivalentes = collect(); // Por defecto vacío

        if ($referencia && $referencia->enlace) {
            $equivalentes = ReferenciaRsf::where('enlace', $referencia->enlace)
                // ->where('codigo_rsf', '!=', $this->codigo_proveedor) // opcional: excluye el actual
                ->get();
        }

        return $equivalentes;

    }

    public static function generarEnlace($codigo)
    {
        $articulo = ReferenciaRsf::where('codigo_barra', $codigo)
        ->orWhere('codigo_rsf', $codigo)
        ->first();

        return $articulo->enlace;
    }



}
