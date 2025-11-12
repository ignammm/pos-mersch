<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Proveedor;
use App\Requests\ArticuloGetRequest;
use App\Requests\ProveedorGetRequest;
use App\Support\RepositoryResult;
use App\Traits\RepositoryResponse;
use App\Traits\ValidatesParameters;
use Illuminate\Validation\ValidationException;

class ProveedoresService
{
    use RepositoryResponse, ValidatesParameters;

    public function get(array $parameters): RepositoryResult
    {
        try {
            $validated = self::validateParams($parameters, ProveedorGetRequest::class);
            $filterColumns = ['created_at', 'updated_at'];
            $filters = GenericQueryService::buildFilters($filterColumns, $validated);
            $proveedores = GenericQueryService::query(
                Proveedor::class,
                $filters,
                [$validated['searchcolumn'] ?? null],
                $validated['searchterm'] ?? null,
                $validated['sortby'] ?? null,
                $validated['order'] ?? 'asc',
                $validated['page'] ?? 1,
                $validated['per_page'] ?? 10,
                $validated['paginate'] ?? true
            );
            return self::successResponse('Proveedores obtenidos correctamente', $proveedores['items']->toArray(), $proveedores['totalPages'], $proveedores['page']);
        } catch (ValidationException $e) {

            return self::errorResponse(
                'Error al obtener los proveedores',
                $e->errors()
            );
        } catch (\Exception $e) {
            return self::errorResponse(
                'Error al obtener los proveedores',
                ['message' => $e->getMessage()]
            );
        }
    }
}
