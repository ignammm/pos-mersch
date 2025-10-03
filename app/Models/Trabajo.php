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
        'presupuesto_id',
        'descripcion',
        'estado',
    ];


    public function vehiculoCliente()
    {
        return $this->belongsTo(VehiculoCliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTrabajo::class);
    }

    public function detallesActivos()
    {
        return $this->hasMany(DetalleTrabajo::class)->where('activo', true);
    }

    public function presupuesto()
    {
        return $this->morphOne(Presupuesto::class, 'conversion', 'tipo_conversion', 'conversion_id');
    }
}