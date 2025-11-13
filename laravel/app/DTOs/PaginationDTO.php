<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class PaginationDTO extends DataTransfer
{
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
        public ?int $previousPage,
        public ?int $nextPage,
        public int $firstPage,
        public int $lastPage
    ) {}
}
