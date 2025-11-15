<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\IngresoService;
use App\Models\Articulo;
use App\Models\ReferenciaRsf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psy\Util\Str;

class IngresoArticuloServiceTest extends TestCase
{
    use RefreshDatabase;

    protected IngresoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new IngresoService();
    }

    /** @test */
    public function devuelve_error_si_el_articulo_no_existe()
    {
        $codigo = 'XYZ999';
        $items = collect();
        $coincidenciasArt = collect();

        $result = $this->service->agregarArticulo($codigo, 1, $items, $coincidenciasArt);

        // ✅ Verificamos que devuelva error
        $this->assertIsArray($result);
        $this->assertEquals('El articulo no existe.', $result['message'] ?? '');
    }

    /** @test */
    public function agrega_articulo_si_existe_una_coincidencia()
    {
        // ✅ Creamos un artículo y una referencia con el mismo código
        $articulo = Articulo::factory()->create([
            'articulo' => 'Filtro de aceite',
            'codigo_interno' => Articulo::generarCodigoInterno(),
            'codigo_proveedor' => 'ABC123',
            'codigo_fabricante' => 'DEF456',
            'marca' => 'Marca 1',
            'rubro' => 'Rubro 1',
            'precio' => 100,
        ]);

        ReferenciaRsf::factory()->create([
            'codigo_barra' => 'ABC123',
            'articulo' => $articulo->articulo,
        ]);

        $items = collect();
        $coincidenciasArt = collect();

        // ✅ Ejecutamos el servicio
        $this->service->agregarArticulo('ABC123', 2, $items, $coincidenciasArt);

        // ✅ Verificamos que haya agregado el artículo al listado
        $this->assertNotEmpty($items);
        $this->assertEquals($articulo->id, $items[0]['articulo_id']);
        $this->assertEquals(200, $items[0]['subtotal']);
    }

    /** @test */
    public function llena_coincidencias_si_hay_multiples_resultados()
    {
        $codigo = 'MULTI001';

        ReferenciaRsf::factory()->count(2)->create(['codigo_barra' => $codigo]);

        $items = collect();
        $coincidenciasArt = collect();

        // ✅ Ejecutamos el servicio
        $this->service->agregarArticulo($codigo, 1, $items, $coincidenciasArt);

        // ✅ Verificamos que haya coincidencias múltiples
        $this->assertTrue($coincidenciasArt->isNotEmpty());
    }
}
