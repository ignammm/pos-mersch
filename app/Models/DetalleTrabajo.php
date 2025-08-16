<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTrabajo extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'articulo_id',
        'observaciones',
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}
