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


    public function vehiculoCliente()
    {
        return $this->belongsTo(VehiculoCliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTrabajo::class);
    }
}