<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\Interfaces\IAbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * base abstract for any project with same arch
 */
abstract class AbstractRepository implements IAbstractRepository
{
    public function __construct(
        protected Model $model
    ) {
    }

    /**
     * Prepare a query builder instance
     */
    public function prepareQuery(): Builder
    {
        return $this->model->query();
    }

    /* -----------------------------------------------------------------
    |  Basic CRUD
    | -----------------------------------------------------------------
    */

    /**
     * Create a new record
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Insert multiple records
     */
    public function insert(array $data): bool
    {
        return $this->model->insert($data);
    }

    /**
     * Update a record by conditions
     */
    public function update(array $conditions, array $data): Model
    {
        /** @var Model $model */
        $model = $this->model->where($conditions)->firstOrFail();
        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete a record by ID
     */
    public function destroy(int $id): int
    {
        return $this->model->destroy($id);
    }

    /**
     * Soft delete a record
     */
    public function softDelete(int $id): ?bool
    {
        $model = $this->findOrFail($id);
        return $model->delete();
    }

    /**
     * Restore a soft-deleted record
     */
    public function restore(int $id): bool
    {
        $model = $this->model->onlyTrashed()->findOrFail($id);
        return $model->restore();
    }

    /**
     * Force delete a record
     */
    public function forceDelete(int $id): ?bool
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        return $model->forceDelete();
    }

    /* -----------------------------------------------------------------
    |  Fetching & Query Helpers
    | -----------------------------------------------------------------
    */

    /**
     * Get the first record
     */
    public function first(): ?Model
    {
        return $this->prepareQuery()->first();
    }

    /**
     * Get all records
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->orderByDesc('id')->select($columns)->get();
    }

    /**
     * Find a record by ID or fail
     */
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->prepareQuery()
            ->select($columns)
            ->with($relations)
            ->findOrFail($id);
    }

    /**
     * Find a record by ID
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->prepareQuery()
            ->select($columns)
            ->with($relations)
            ->find($id);
    }

    /**
     * Get records with relations
     */
    public function getWith(array $with): Collection
    {
        return $this->model->with($with)->get();
    }

    /**
     * Get records by condition
     */
    public function getByCondition(string $column, mixed $value, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->prepareQuery()
            ->select($columns)
            ->with($relations)
            ->where($column, $value)
            ->get();
    }


}
