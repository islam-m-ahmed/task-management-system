<?php

namespace App\Architecture\Repositories\Interfaces;

interface ITaskDependencyRepository extends IAbstractRepository
{
    public function addDependency(int $taskId, int $dependsOnTaskId): bool;

    public function dependencyExists(int $taskId, int $dependsOnTaskId): bool;

    public function hasIncompleteDependencies(int $taskId): bool;
}
