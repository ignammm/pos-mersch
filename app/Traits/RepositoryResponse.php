<?php

namespace App\Traits;

use App\Support\RepositoryResult;

trait RepositoryResponse
{
    protected function successResponse(
        string $message = 'Operación exitosa',
        array $items = [],
        ?int $totalPages = null,
        ?int $page = null
    ): RepositoryResult {
        return RepositoryResult::success($message, $items, $totalPages, $page);
    }

    protected function errorResponse(
        string $message = 'Ocurrió un error',
        array $errors = []
    ): RepositoryResult {
        return RepositoryResult::error($message, $errors);
    }
}
