<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Factura>
 */
class FacturaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_comprobante' => 'Ticket',
            'numero' => 1,
            'user_id' => 1,
            'cliente_id' => 1,
            'monto_original' => 1000,
            'fecha' => now(),
        ];
    }
}
