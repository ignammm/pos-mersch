<?php

namespace App\Support;

class RepositoryErrors
{
    /** @var array<string, string> */
    public array $fields;

    public function __construct(array $errors = [])
    {
        $this->fields = $errors;
    }

    public function get(string $field): ?string
    {
        return $this->fields[$field] ?? null;
    }
}
