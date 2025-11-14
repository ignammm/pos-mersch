<?php

namespace App\Services;

use App\Models\Articulo;
use App\Requests\ArticuloGetRequest;
use App\Support\RepositoryResult;
use App\Traits\RepositoryResponse;
use App\Traits\ValidatesParameters;
use Illuminate\Validation\ValidationException;
use LDAP\Result;

class ArticulosService
{
    use RepositoryResponse, ValidatesParameters;

    public function get(array $parameters): RepositoryResult
    {
        try {
            $validated = self::validateParams($parameters, ArticuloGetRequest::class);
            $filterColumns = ['articulo', 'codigo_interno', 'codigo_fabricante', 'proveedor.id', 'marca'];
            $filters = GenericQueryService::buildFilters($filterColumns, $validated);
            $articulos = GenericQueryService::query(
                Articulo::class,
                $filters,
                [$validated['searchcolumn'] ?? null],
                $validated['searchterm'] ?? null,
                $validated['sortby'] ?? null,
                $validated['order'] ?? 'asc',
                $validated['page'] ?? 1,
                $validated['per_page'] ?? 10,
                $validated['paginate'] ?? true,
                ['proveedor', 'stock']
            );
            return self::successResponse('Articulos obtenidos correctamente', $articulos['items']->toArray(), $articulos['totalPages'], $articulos['page']);
        } catch (ValidationException $e) {

            return self::errorResponse(
                'Error al obtener los artículos',
                $e->errors()
            );
        } catch (\Exception $e) {
            return self::errorResponse(
                'Error al obtener los artículos',
                ['message' => $e->getMessage()]
            );
        }
    }

    public function deleteone(int $id): RepositoryResult
    {
        try {
            $articulo = Articulo::find($id);
            if (!$articulo) {
                return new RepositoryResult(false, 'No se encontró el artículo');
            }
            $articulo->delete();
            return new RepositoryResult(true, 'Se ha eliminado el artículo');
        } catch (\Exception $e) {
            return new RepositoryResult(false, $e->getMessage());
        }
    }
}
