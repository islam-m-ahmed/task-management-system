<?php

namespace App\Architecture\Repositories\Interfaces;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ITaskRepository extends IAbstractRepository
{
    public function getFilteredTasks(
        ?TaskStatus $status = null,
        ?string $dueDateFrom = null,
        ?string $dueDateTo = null,
        ?int $assignedUserId = null
    ): Collection;

    public function getTaskWithDependencies(int $taskId);

}
