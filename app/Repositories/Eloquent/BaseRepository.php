<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\FilterOptionsDTO;
use App\DTOs\PaginationDTO;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\IRepository;
use Domain\Shared\Attributes\SkipPersistence;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;
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
    protected function convertToEntity(array $data): object
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

    protected function convertToModel(object $entity): array
    {
        $data = $this->hydrator->extract($entity);

        $snakeCaseData = [];

        $reflection = new ReflectionClass($entity);
        $properties = $reflection->getProperties();

        // create a map of properties to skip
        $skipProperties = [];
        foreach ($properties as $property) {
            if (!empty($property->getAttributes(SkipPersistence::class))) {
                $skipProperties[] = $property->getName();
            }
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $skipProperties, true)) {
                continue;
            }

            $snakeKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
            $snakeCaseData[$snakeKey] = $value;
        }

        return $snakeCaseData;
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
        /**
         * @throws ReflectionException
         */
            fn(Model $item): object => $this->convertToEntity($item->toArray()),
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
                $direction = strtolower($filterOptions->orderDirection ?? 'asc') === 'desc' ? 'desc' : 'asc';
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
     * @throws ReflectionException
     * @throws ResourceNotFoundException
     */
    public function findOne(EloquentQueryBuilder|array|null $filter = [], ?FilterOptionsDTO $filterOptions = null, bool $throwException = true): ?object
    {
        $query = $this->getQueryFromFilter($filter, $filterOptions);

        $result = $query->first();

        $result =  $result ? $this->convertToEntity($result->toArray()) : null;

        if ($throwException && !$result) {
            throw new ResourceNotFoundException("Entity of type {$this->entityClass} not found with the provided filter.");
        }

        return $result;
    }

    /**
     * @return TEntity|null
     * @throws ReflectionException
     */
    public function findById(int $id): ?object
    {
        return $this->findOne(['id' => $id]);
    }

    public function exists(array $data): bool
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
     * @throws ReflectionException
     */
    public function create(array $data): object
    {
        /** @var Model $model */
        $model = $this->ormObject->create($data);
        return $this->convertToEntity($model->toArray());
    }

    /**
     * @return TEntity
     * @throws ReflectionException
     */
    public function update(int $id, array $data): object
    {
        /** @var Model $model */
        $model = $this->ormObject->findOrFail($id);

        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }

        $model->save();

        return $this->convertToEntity($model->toArray());
    }

    public function destroy(int $id): bool
    {
        /** @var Model $model */
        $model = $this->ormObject->findOrFail($id);
        return $model->delete();
    }

    /**
     * @param TEntity $entity
     * @return TEntity
     * @throws InvalidArgumentException
     */
    public function persistEntity(object $entity): object
    {
        if (!$entity instanceof $this->entityClass) {
            throw new InvalidArgumentException("Entity must be an instance of {$this->entityClass}");
        }

        $rawData = $this->convertToModel($entity);
        $fillable = $this->ormObject->getFillable();
        $data = array_intersect_key($rawData, array_flip($fillable));

        if ($entity->id) {
            return $this->update($entity->id, $data);
        }

        return $this->create($data);
    }
}
