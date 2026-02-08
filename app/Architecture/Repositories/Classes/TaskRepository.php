<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\Interfaces\ITaskRepository;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TaskRepository extends AbstractRepository implements ITaskRepository
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    /**
     * Get tasks with filters
     */
    public function getFilteredTasks(
        ?TaskStatus $status = null,
        ?string $dueDateFrom = null,
        ?string $dueDateTo = null,
        ?int $assignedUserId = null
    ): Collection {
        $query = $this->prepareQuery()
            ->with(['assignedUser', 'createdBy', 'dependencies', 'dependents']);

        if ($status) {
            $query->where('status', $status->value);
        }

        if ($dueDateFrom) {
            $query->whereDate('due_date', '>=', $dueDateFrom);
        }

        if ($dueDateTo) {
            $query->whereDate('due_date', '<=', $dueDateTo);
        }

        if ($assignedUserId) {
            $query->where('assigned_user_id', $assignedUserId);
        }

        return $query->orderByDesc('created_at')->get();
    }



    /**
     * Get task with all dependencies
     */
    public function getTaskWithDependencies(int $taskId)
    {
        return $this->prepareQuery()
            ->with([
                'assignedUser',
                'createdBy',
                'dependencies.dependency',
                'dependents.dependent'
            ])
            ->find($taskId);
    }

}
