<?php

namespace Tests\Unit;

use App\Models\ReferenciaRsf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticuloScopeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function devuelve_articulo_cuando_el_codigo_coincide_con_codigo_rsf()
    {
        $articulo = ReferenciaRsf::factory()->create([
            'codigo_rsf' => 'RSF123',
            'codigo_barra' => 'BAR111',
            'articulo' => 'Filtro de aceite',
        ]);

        $resultado = ReferenciaRsf::getByCodigo('RSF123')->first();

        $this->assertNotNull($resultado);
        $this->assertEquals($articulo->id, $resultado->id);
    }

    /** @test */
    public function devuelve_articulo_cuando_el_codigo_coincide_con_codigo_barra()
    {
        $articulo = ReferenciaRsf::factory()->create([
            'codigo_rsf' => 'RSF999',
            'codigo_barra' => 'BAR555',
            'articulo' => 'Pastilla de freno',
        ]);

        $resultado = ReferenciaRsf::getByCodigo('BAR555')->first();

        $this->assertNotNull($resultado);
        $this->assertEquals($articulo->id, $resultado->id);
    }

    /** @test */
    public function devuelve_articulo_cuando_el_codigo_coincide_con_nombre()
    {
        $articulo = ReferenciaRsf::factory()->create([
            'codigo_rsf' => 'RSF001',
            'codigo_barra' => 'BAR002',
            'articulo' => 'Filtro de aire',
        ]);

        $resultado = ReferenciaRsf::getByCodigo('Filtro de aire')->first();

        $this->assertNotNull($resultado);
        $this->assertEquals($articulo->id, $resultado->id);
    }

    /** @test */
    public function no_devuelve_nada_cuando_no_hay_coincidencias()
    {
        ReferenciaRsf::factory()->create([
            'codigo_rsf' => 'AAA111',
            'codigo_barra' => 'BBB222',
            'articulo' => 'Aceite 10W40',
        ]);

        $resultado = ReferenciaRsf::getByCodigo('ZZZ999')->first();

        $this->assertNull($resultado);
    }
}