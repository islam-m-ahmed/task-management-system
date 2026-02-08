<?php

namespace App\Http\Controllers\Api\V1;

use App\Architecture\Services\Interfaces\ITaskService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\AddDependencyRequest;
use App\Http\Requests\Task\ListTasksRequest;
use App\Http\Requests\Task\ShowTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(private readonly ITaskService $taskService)
    {
        $this->middleware('permission:task:create')->only('store');
        $this->middleware('permission:task:update')->only(['update', 'addDependency']);
        $this->middleware('permission:task:update-status')->only('updateStatus');
        $this->middleware('task.access')->only(['show', 'updateStatus', 'update', 'addDependency']);
    }

    /**
     * List all tasks (managers see all, users see assigned only).
     */
    public function index(ListTasksRequest $request): JsonResponse
    {
        return $this->taskService->getAllTasks($request->safe()->toArray());
    }

    /**
     * Get task details with dependencies.
     */
    public function show(ShowTaskRequest $request): JsonResponse
    {
        return $this->taskService->getTask($request->getTaskId());
    }

    /**
     * Create a new task. (Manager only)
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        return $this->taskService->createTask($request->safe()->toArray());
    }

    /**
     * Update task details. (Manager only)
     */
    public function update(UpdateTaskRequest $request): JsonResponse
    {
        return $this->taskService->updateTask($request->safe()->toArray());
    }

    /**
     * Update task status. (Assigned user only)
     */
    public function updateStatus(UpdateTaskStatusRequest $request): JsonResponse
    {
        return $this->taskService->updateTaskStatus($request->safe()->toArray());
    }

    /**
     * Add task dependency. (Manager only)
     */
    public function addDependency(AddDependencyRequest $request): JsonResponse
    {
        return $this->taskService->addDependency($request->safe()->toArray());
    }
}
