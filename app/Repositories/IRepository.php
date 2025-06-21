<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\FilterOptionsDTO;
use App\DTOs\PaginationDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;

/**
 * @template TEntity of object
 */
interface IRepository
{
    public function getQueryBuilder(): Builder; // we can change for mixed in the future

    public function for(string $entityClass): self;

    public function findAll(?array $filter = null, ?int $take = 15, int $page = 1, ?FilterOptionsDTO $filterOptions = null): PaginationDTO;

    /**
     * @return TEntity|null
     */
    public function findOne(EloquentQueryBuilder|array|null $filter = [], ?FilterOptionsDTO $filterOptions = null): ?object;

    /**
     * @return TEntity|null
     */
    public function findById(int $id): ?object;

    /**
     * @return TEntity
     */
    public function create(array $data): object;

    /**
     * @return TEntity
     */
    public function update(int $id, array $data): object;

    public function destroy(int $id): bool;
}

