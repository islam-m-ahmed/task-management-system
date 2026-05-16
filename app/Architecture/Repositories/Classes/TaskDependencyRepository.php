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
     * Check if adding a dependency would create a circular reference
     * Uses BFS to traverse the full dependency chain at any depth
     */
    public function hasCircularDependency(int $taskId, int $dependsOnTaskId): bool
    {
        $visited = [];
        $queue = [$dependsOnTaskId];

        while (!empty($queue)) {
            $currentTaskId = array_shift($queue);

            // If we reached the original task, it's a circular dependency
            if ($currentTaskId === $taskId) {
                return true;
            }

            // Skip already visited tasks to avoid infinite loops
            if (in_array($currentTaskId, $visited)) {
                continue;
            }

            $visited[] = $currentTaskId;

            // Get all tasks that $currentTaskId depends on
            $dependencies = $this->prepareQuery()
                ->where('task_id', $currentTaskId)
                ->pluck('depends_on_task_id')
                ->toArray();

            foreach ($dependencies as $depId) {
                if (!in_array($depId, $visited)) {
                    $queue[] = $depId;
                }
            }
        }

        return false;
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
