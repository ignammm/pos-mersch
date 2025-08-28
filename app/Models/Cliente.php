<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'dni',
        'telefono',
        'direccion',
        'email',
        'percepcion_iva',
        'cuit',
        'tipo_cliente',
    ];


    public function facturas()
    {
        return $this->hasMany(Cliente::class);
    }

    public function vehiculosCliente()
    {
        return $this->hasMany(VehiculoCliente::class);
    }
}
