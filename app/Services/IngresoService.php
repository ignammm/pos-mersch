<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\ReferenciaRsf;

use function Laravel\Prompts\error;

class IngresoService
{
    public function agregarArticulo($codigo_barra, $cantidad, &$items, &$coincidenciasArt, $proveedor_id = null)
    {
        $refItems = ReferenciaRsf::getByCodigo($codigo_barra);

        if (!ReferenciaRsf::existsReferenciaRsf($codigo_barra)) {
            return [
                'field' => 'codigo_barra',
                'message' => 'El articulo no existe.'
            ];
        };

        $cantidadRefItems = $refItems->count();

        if ($cantidadRefItems > 1) {
            $coincidenciasArt = $refItems->get();
            return;
        }

        if ($cantidadRefItems === 1) {
            $artNuevo =  $this->crearArticulos($refItems->first(), $cantidad, $proveedor_id);
            $this->agregarArticuloListado($artNuevo, $cantidad, $items);
            return;
        }

    }

    private function agregarArticuloListado($articulo, $cantidad, &$items)
    {
        $items[] = [
            'articulo_id' => $articulo->id,
            'nombre' => $articulo->articulo,
            'rubro' => $articulo->rubro,
            'marca' => $articulo->marca,
            'codigo_proveedor' => $articulo->codigo_proveedor,
            'codigo_fabricante' => $articulo->codigo_fabricante,
            'cantidad' => $cantidad,
            'precio_unitario' => $articulo->precio,
            'subtotal' => ($cantidad * $articulo->precio),
        ];
    }

    private function crearArticulos($articulo_rsf, $cantidad, $proveedor_id)
    {
        $articulo = Articulo::create([
            'articulo' => $articulo_rsf->articulo,
            'codigo_interno' => Articulo::generarCodigoInterno(),
            'codigo_proveedor' => $articulo_rsf->codigo_rsf,
            'codigo_fabricante' => $articulo_rsf->codigo_barra,
            'rubro' => $articulo_rsf->tipo_txt,
            'precio' => round($articulo_rsf->precio_lista, 0),
            'stock' => $cantidad,
            'marca' => $articulo_rsf->marca_rsf,
            'descripcion' => $articulo_rsf->descripcion,
            'enlace' => $articulo_rsf->enlace,
            'unidad' => $articulo_rsf->modulo_venta,
            'proveedor_id' => $proveedor_id,
        ]);

        return $articulo;
    }
}
