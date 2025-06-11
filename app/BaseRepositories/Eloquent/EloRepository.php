<?php

declare(strict_types=1);

namespace App\BaseRepositories\Eloquent;

use App\Domain\Shared\DTOs\FilterOptionsDTO;
use App\Domain\Shared\DTOs\PaginationDTO;
use App\BaseRepositories\IRepository;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class EloRepository implements IRepository
{
    protected string $modelClass = '';
    protected Model $modelObject;

    /**
     * Here we work with the same instance
     */
    public function __construct()
    {
        if (!empty($this->modelClass)) {
            $instance = $this->for($this->modelClass);
            $this->modelObject = $instance->modelObject;
        }
    }

    /**
     * Here we work with a new instance
     *
     * @param string $className
     * @return $this
     */
    public function for(string $className): static
    {
        $new = clone $this;
        $schema = str_contains($className, '\\') ? $className : "App\\Models\\{$className}";
        $new->modelObject = app($schema);
        return $new;
    }

    public function getQueryBuilder(): Builder
    {
        return $this->modelObject->newQuery();
    }

    public function getEntity(): mixed
    {
        return $this->modelObject;
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

        return new PaginationDTO(
            collect($paginated->items())
                ->map(fn($item) => new ArrayObject($item->toArray(), ArrayObject::ARRAY_AS_PROPS))
                ->all(),
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

    public function findOne(EloquentQueryBuilder|array|null $filter = [], ?FilterOptionsDTO $filterOptions = null): ?ArrayObject
    {
        $query = $this->getQueryFromFilter($filter, $filterOptions);

        $model = $query->first();

        return $model ? new ArrayObject($model->toArray(), ArrayObject::ARRAY_AS_PROPS) : null;
    }

    public function findById(int $id): ?ArrayObject
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

    public function create(array $data): ArrayObject
    {
        $model = $this->modelObject->create($data);

        return new ArrayObject($model->toArray(), ArrayObject::ARRAY_AS_PROPS);
    }

    public function update(int $id, array $data): ArrayObject
    {
        $model = $this->modelObject->findOrFail($id);

        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }

        $model->save();

        return new ArrayObject($model->toArray(), ArrayObject::ARRAY_AS_PROPS);
    }

    public function destroy(int $id): bool
    {
        $model = $this->modelObject->findOrFail($id);
        return $model->delete();
    }
}
