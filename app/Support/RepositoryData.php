<?php

namespace App\Support;

class RepositoryData
{
    /** @var array<mixed> */
    public array $items;
    public ?int $totalPages;
    public ?int $page;

    public function __construct(array $items = [], ?int $totalPages = null, ?int $page = null)
    {
        $this->items = $items;
        $this->totalPages = $totalPages;
        $this->page = $page;
    }
}
