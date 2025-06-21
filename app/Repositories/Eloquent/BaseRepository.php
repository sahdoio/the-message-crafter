<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\FilterOptionsDTO;
use App\DTOs\PaginationDTO;
use App\Repositories\IRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Laminas\Hydrator\ReflectionHydrator;
use ReflectionClass;
use ReflectionException;

/**
 * @template TEntity of object
 */
class BaseRepository implements IRepository
{
    public Model $ormObject {
        get {
            return $this->ormObject;
        }
    }

    /** @var class-string<TEntity> */
    protected string $entityClass;
    protected ReflectionHydrator $hydrator;

    /**
     * Here we work with the same instance
     */
    public function __construct()
    {
        if (!empty($this->entityClass)) {
            $instance = $this->for($this->entityClass);
            $this->ormObject = $instance->ormObject;
            $this->entityClass = $instance->entityClass;
            $this->hydrator = $instance->hydrator;
        }
    }

    public function for(string $entityClass): self
    {
        $new = clone $this;
        $new->entityClass = $entityClass;
        $modelClass = $this->resolveModelFromEntity($entityClass);
        $new->ormObject = app($modelClass);
        $new->hydrator = new ReflectionHydrator();
        return $new;
    }

    protected function resolveModelFromEntity(string $entityClass): string
    {
        $classBaseName = class_basename($entityClass);
        return "App\\Models\\{$classBaseName}";
    }

    /**
     * Creates an entity instance from a database record
     * @throws ReflectionException
     */
    protected function createEntity(array $data): object
    {
        $reflection = new ReflectionClass($this->entityClass);
        $entity = $reflection->newInstanceWithoutConstructor();

        $camelCaseData = [];
        foreach ($data as $key => $value) {
            $camelKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            $camelCaseData[$camelKey] = $value;
        }

        return $this->hydrator->hydrate($camelCaseData, $entity);
    }

    public function getQueryBuilder(): Builder
    {
        return $this->ormObject->newQuery();
    }

    public function findAll(EloquentQueryBuilder|array|null $filter = [], ?int $take = 15, int $page = 1, ?FilterOptionsDTO $filterOptions = null): PaginationDTO
    {
        $query = $this->getQueryFromFilter($filter, $filterOptions);

        $paginated = $query->paginate(
            $take,
            ['*'],
            'page',
            $page
        );

        $entities = array_map(
            fn(Model $item): object => $this->createEntity($item->toArray()),
            $paginated->items()
        );

        return new PaginationDTO(
            $entities,
            $paginated->total(),
            $paginated->currentPage(),
            $paginated->perPage(),
            $paginated->currentPage() > 1
                ? $paginated->currentPage() - 1
                : null,
            $paginated->currentPage() < $paginated->lastPage()
                ? $paginated->currentPage() + 1
                : null,
            1,
            $paginated->lastPage(),
        );
    }

    private function getQueryFromFilter(EloquentQueryBuilder|array|null $filter, ?FilterOptionsDTO $filterOptions = null): EloquentQueryBuilder
    {
        if ($filter instanceof EloquentQueryBuilder) {
            $query = $filter;
        } elseif (is_array($filter)) {
            $query = $this->getQueryBuilder();
            foreach ($filter as $key => $value) {
                if (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        } else {
            $query = $this->getQueryBuilder();
        }

        // apply FilterOptionsDTO if provided
        if (!is_null($filterOptions)) {
            if (!empty($filterOptions->orderBy)) {
                $direction = strtolower($filterOptions->sortDirection ?? 'asc') === 'desc' ? 'desc' : 'asc';
                $query->orderBy($filterOptions->orderBy, $direction);
            }

            if (!empty($filterOptions->limit)) {
                $query->limit($filterOptions->limit);
            }

            if (!empty($filterOptions->offset)) {
                $query->offset($filterOptions->offset);
            }
        }

        return $query;
    }

    /**
     * @return TEntity|null
     */
    public function findOne(EloquentQueryBuilder|array|null $filter = [], ?FilterOptionsDTO $filterOptions = null): ?object
    {
        $query = $this->getQueryFromFilter($filter, $filterOptions);

        $result = $query->first();

        return $result ? $this->createEntity($result->toArray()) : null;
    }

    /**
     * @return TEntity|null
     */
    public function findById(int $id): ?object
    {
        return $this->findOne(['id' => $id]);
    }

    public function exist(array $data): bool
    {
        $query = $this->getQueryBuilder();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->exists();
    }

    /**
     * @return TEntity
     */
    public function create(array $data): object
    {
        $model = $this->ormObject->create($data);
        return $this->createEntity($model->toArray());
    }

    /**
     * @return TEntity
     */
    public function update(int $id, array $data): object
    {
        $model = $this->ormObject->findOrFail($id);

        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }

        $model->save();

        return $this->createEntity($model->toArray());
    }

    public function destroy(int $id): bool
    {
        $model = $this->ormObject->findOrFail($id);
        return $model->delete();
    }
}
