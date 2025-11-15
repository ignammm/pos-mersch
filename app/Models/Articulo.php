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
        'stock',
        'marca',
        'descripcion',
        'enlace',
        'unidad',
        'proveedor_id',
        'stock_minimo',
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

    public static function getByCodigo($codigo)
    {
        return Articulo::where('codigo_proveedor', $codigo)
            ->orWhere('codigo_fabricante', $codigo)
            ->orWhere('articulo', $codigo);
    }

    public static function existsArticulo($codigo_barra)
    {
        return ReferenciaRsf::where('codigo_proveedor', $codigo_barra)
            ->orWhere('codigo_fabricante', $codigo_barra)
            ->orWhere('articulo', $codigo_barra)
            ->exists();
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
