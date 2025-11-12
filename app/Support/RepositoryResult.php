<?php

namespace App\Support;

class RepositoryResult
{
    public bool $successful;
    public string $message;
    public ?RepositoryData $data;
    public ?RepositoryErrors $errors;

    public function __construct(
        bool $successful,
        string $message,
        ?RepositoryData $data = null,
        ?RepositoryErrors $errors = null
    ) {
        $this->successful = $successful;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
    }

    public static function success(
        string $message = 'Operación exitosa',
        array $items = [],
        ?int $totalPages = null,
        ?int $page = null
    ): self {
        $data = new RepositoryData($items, $totalPages, $page);
        return new self(true, $message, $data, null);
    }

    public static function error(
        string $message = 'Ocurrió un error',
        array $errors = []
    ): self {
        $errorObject = new RepositoryErrors($errors);
        return new self(false, $message, null, $errorObject);
    }
}
