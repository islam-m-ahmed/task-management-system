<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\Interfaces\ITaskDependencyRepository;
use App\Enums\TaskStatus;
use App\Models\TaskDependency;

class TaskDependencyRepository extends AbstractRepository implements ITaskDependencyRepository
{
    public function __construct(TaskDependency $model)
    {
        parent::__construct($model);
    }

    /**
     * Add a dependency between two tasks
     */
    public function addDependency(int $taskId, int $dependsOnTaskId): bool
    {
        if ($this->dependencyExists($taskId, $dependsOnTaskId)) {
            return false;
        }

        $this->create([
            'task_id' => $taskId,
            'depends_on_task_id' => $dependsOnTaskId,
        ]);

        return true;
    }

    /**
     * Check if a dependency exists
     */
    public function dependencyExists(int $taskId, int $dependsOnTaskId): bool
    {
        return $this->prepareQuery()
            ->where('task_id', $taskId)
            ->where('depends_on_task_id', $dependsOnTaskId)
            ->exists();
    }


    /**
     * Check if a task has any incomplete dependencies
     */
    public function hasIncompleteDependencies(int $taskId): bool
    {
        return $this->prepareQuery()
            ->where('task_id', $taskId)
            ->whereHas('dependency', function ($query) {
                $query->where('status', '!=', TaskStatus::COMPLETED->value);
            })
            ->exists();
    }
}
