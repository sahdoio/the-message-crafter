<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\FilterOptionsDTO;
use App\DTOs\PaginationDTO;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;

interface IRepository
{
    public function getQueryBuilder(): Builder; // we can change for mixed in the future

    public function getEntity(): mixed;

    public function for(string $className): static;

    public function findAll(?array $filter = null, ?int $take = 15, int $page = 1, ?FilterOptionsDTO $filterOptions = null): PaginationDTO;

    public function findOne(EloquentQueryBuilder|array|null $filter = [], ?FilterOptionsDTO $filterOptions = null): ?ArrayObject;

    public function findById(int $id): ?ArrayObject;

    public function create(array $data): ArrayObject;

    public function update(int $id, array $data): ArrayObject;

    public function destroy(int $id): bool;
}

