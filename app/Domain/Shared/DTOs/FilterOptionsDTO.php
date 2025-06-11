<?php

declare(strict_types=1);

namespace App\Domain\Shared\DTOs;

readonly class FilterOptionsDTO extends DataTransfer
{
    public function __construct(
        public ?int $limit = null,
        public ?int $offset = null,
        public ?string $orderBy = null,
        public string $sortDirection = 'asc',
    ) {}
}
