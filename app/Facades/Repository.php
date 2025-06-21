<?php

declare(strict_types=1);

namespace App\Facades;

use App\DTOs\PaginationDTO;
use App\Repositories\IRepository;
use ArrayObject;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder getQueryBuilder()
 * @method static mixed getEntity()
 * @method static static for (string $entityClass)
 * @method static PaginationDTO findAll(?array $filter = null, ?int $take = 15, int $page = 1)
 * @method static ArrayObject|null findOne(array $filter = [])
 * @method static ArrayObject|null findById(int $id)
 * @method static ArrayObject create(array $data)
 * @method static ArrayObject update(int $id, array $data)
 * @method static bool destroy(int $id)
 */
class Repository extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return IRepository::class;
    }
}
