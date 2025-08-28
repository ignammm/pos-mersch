<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculoCliente extends Model
{
    use HasFactory;

    protected $table = 'vehiculo_cliente'; 
    
    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'patente',
    ];

    public function cliente()
    {
        return $this->belongsTo(cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
