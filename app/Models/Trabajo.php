<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha',
        'vehiculo_cliente_id',
        'descripcion',
    ];


    public function vehiculo_cliente()
    {
        return $this->belongsTo(VehiculoCliente::class);
    }
}
