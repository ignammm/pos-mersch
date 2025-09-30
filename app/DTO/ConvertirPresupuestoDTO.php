<?php

namespace App\DTO;

class ConvertirPresupuestoDTO
{
    public function __construct(
        public string $tipo,
        public int $cliente_id,
        public ?int $vehiculo_cliente_id = null,
        public ?string $nombre_trabajo = null,
        public ?string $descricion_trabajo = null,
    ) {}
    
    public static function fromRequest(array $data): self
    {
        return new self(
            tipo: $data['tipo'],
            cliente_id: $data['cliente_id'],
            vehiculo_cliente_id: $data['vehiculo_cliente_id'] ?? null,
            nombre_trabajo: $data['nombre_trabajo'] ?? null,
            descricion_trabajo: $data['descricion_trabajo'] ?? null
        );
    }
}