<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class GenericQueryService
{
    /**
     * Generic filter, sort, and optional paginate query
     *
     * @param string $model Fully qualified Eloquent model class
     * @param array $filters ['field' => 'value', ...]
     * @param array $searchColumns Columns to apply search filter
     * @param string|null $searchTerm Search string
     * @param string|null $sortField
     * @param string $sortDirection
     * @param int $page
     * @param int $perPage
     * @param bool $paginate
     * @param array $with
     */
    public static function query(
        string $model,
        array $filters = [],
        array $searchColumns = [],
        ?string $searchTerm = null,
        ?string $sortField = null,
        string $sortDirection = 'asc',
        int $page = 1,
        int $perPage = 10,
        bool $paginate = true,
        array $with = []
    ): array {
        if (!class_exists($model)) {
            throw new \InvalidArgumentException("El modelo {$model} no existe");
        }

        foreach ($with as $relation) {
            if (!method_exists($model, $relation)) {
                throw new \InvalidArgumentException("La relación {$relation} no existe en el modelo {$model}");
            }
        }

        $query = $model::query();

        if (!empty($with)) {
            $query->with($with);
        }

        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                if (str_contains($field, '.')) {
                    [$relation, $column] = explode('.', $field, 2);
                    $query->whereHas($relation, function ($q) use ($column, $value) {
                        $q->where($column, $value);
                    });
                } else {
                    $query->where($field, $value);
                }
            }
        }

        if ($searchTerm && !empty($searchColumns)) {
            $normalized = strtolower(trim($searchTerm));
            $query->where(function ($q) use ($searchColumns, $normalized) {
                foreach ($searchColumns as $column) {
                    if (str_contains($column, '.')) {
                        [$relation, $relColumn] = explode('.', $column, 2);
                        $q->orWhereHas($relation, function ($subQ) use ($relColumn, $normalized) {
                            $subQ->whereRaw("LOWER({$relColumn}) LIKE ?", ["%{$normalized}%"]);
                        });
                    } else {
                        $q->orWhereRaw("LOWER({$column}) LIKE ?", ["%{$normalized}%"]);
                    }
                }
            });
        }

        if ($sortField) {
            if (str_contains($sortField, '.')) {
                [$relation, $column] = explode('.', $sortField, 2);
                $query->join(
                    (new $model)->$relation()->getRelated()->getTable() . ' as ' . $relation,
                    (new $model)->getTable() . '.' . (new $model)->$relation()->getForeignKeyName(),
                    '=',
                    $relation . '.id'
                )->orderBy($relation . '.' . $column, $sortDirection)
                    ->select((new $model)->getTable() . '.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        $total = $query->count();

        if ($paginate) {
            $items = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            $totalPages = ceil($total / $perPage);
        } else {
            $items = $query->get();
            $page = 1;
            $totalPages = 1;
        }

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
        ];
    }

    public static function buildFilters(array $fields, $validatedRequest)
    {
        $filters = [];

        foreach ($fields as $field) {
            $value = data_get($validatedRequest, $field);
            if (!empty($value)) {
                $filters[$field] = $value;
            }
        }
        return $filters;
    }
}
