<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;

    protected $table = 'metodos_pago';
    
    protected $fillable = [
        'nombre',
        'tipo',
        'comision_porcentaje',
        'comision_fija',
        'dias_liquidacion',
        'activo',
        'configuracion'
    ];
    
    protected $casts = [
        'comision_porcentaje' => 'decimal:2',
        'comision_fija' => 'decimal:2',
        'dias_liquidacion' => 'integer',
        'activo' => 'boolean',
        'configuracion' => 'array'
    ];
    
    const TIPOS = [
        'EFECTIVO' => 'efectivo',
        'TARJETA_CREDITO' => 'tarjeta_credito',
        'TARJETA_DEBITO' => 'tarjeta_debito',
        'TRANSFERENCIA' => 'transferencia',
        'CHEQUE' => 'cheque',
        'CUENTA_CORRIENTE' => 'cuenta_corriente',
        'OTROS' => 'otros'
    ];
    
    // Relaciones
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
    
    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class);
    }
    
    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    
    public function scopeParaFacturas($query)
    {
        return $query->where('tipo', '!=', self::TIPOS['CUENTA_CORRIENTE']);
    }
    
    // Métodos de negocio
    public function calcularComision($monto)
    {
        $comisionPorcentual = ($monto * $this->comision_porcentaje) / 100;
        return $comisionPorcentual + $this->comision_fija;
    }
    
    public function getNeto($monto)
    {
        return $monto - $this->calcularComision($monto);
    }
    
    public function requiereCaja()
    {
        return in_array($this->tipo, [
            self::TIPOS['EFECTIVO'],
            self::TIPOS['TARJETA_CREDITO'],
            self::TIPOS['TARJETA_DEBITO']
        ]);
    }
    
    public function esElectronico()
    {
        return in_array($this->tipo, [
            self::TIPOS['TARJETA_CREDITO'],
            self::TIPOS['TARJETA_DEBITO'],
            self::TIPOS['TRANSFERENCIA'],
        ]);
    }
    
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} ({$this->comision_porcentaje}% + \${$this->comision_fija})";
    }
}