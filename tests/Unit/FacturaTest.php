<?php

namespace Tests\Unit;

use App\Models\Factura;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FacturaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function calcula_el_siguiente_numero_de_comprobante_para_ticket()
    {
        // Creamos algunas facturas de prueba
        Factura::factory()->create(['tipo_comprobante' => 'Ticket', 'numero' => 12]);
        Factura::factory()->create(['tipo_comprobante' => 'Ticket', 'numero' => 15]);
        Factura::factory()->create(['tipo_comprobante' => 'Factura A', 'numero' => 17]);

        // Obtenemos la última factura del tipo deseado
        $ultimaFactura = Factura::where('tipo_comprobante', 'Ticket')
            ->orderByDesc('numero')
            ->first();

        $siguienteNro = $ultimaFactura ? $ultimaFactura->numero + 1 : 1;

        dump("Último Ticket: {$ultimaFactura->numero}, Siguiente: {$siguienteNro}");

        $this->assertEquals(16, $siguienteNro);
    }
}
