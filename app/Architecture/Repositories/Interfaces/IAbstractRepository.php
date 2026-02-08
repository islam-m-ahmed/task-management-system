<?php

namespace App\Architecture\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface IAbstractRepository
{
    /**
     * Prepare a query builder instance
     */
    public function prepareQuery(): Builder;

    /* -----------------------------------------------------------------
    |  Basic CRUD
    | -----------------------------------------------------------------
    */

    /**
     * Create a new record
     */
    public function create(array $data): Model;

    /**
     * Insert multiple records
     */
    public function insert(array $data): bool;

    /**
     * Update a record by conditions
     */
    public function update(array $conditions, array $data): Model;

    /**
     * Delete a record by ID
     */
    public function destroy(int $id): int;

    /**
     * Soft delete a record
     */
    public function softDelete(int $id): ?bool;

    /**
     * Restore a soft-deleted record
     */
    public function restore(int $id): bool;

    /**
     * Force delete a record
     */
    public function forceDelete(int $id): ?bool;

    /* -----------------------------------------------------------------
    |  Fetching & Query Helpers
    | -----------------------------------------------------------------
    */

    /**
     * Get the first record
     */
    public function first(): ?Model;

    /**
     * Get all records
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Find a record by ID or fail
     */
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Find a record by ID
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Get records with relations
     */
    public function getWith(array $with): Collection;

    /**
     * Get records by condition
     */
    public function getByCondition(string $column, mixed $value, array $columns = ['*'], array $relations = []): Collection;


}
