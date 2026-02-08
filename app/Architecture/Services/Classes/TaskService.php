<?php

namespace App\Architecture\Services\Classes;

use App\Architecture\Repositories\Interfaces\ITaskDependencyRepository;
use App\Architecture\Repositories\Interfaces\ITaskRepository;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Interfaces\ITaskService;
use App\Enums\TaskStatus;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TaskService implements ITaskService
{
    public function __construct(
        private readonly ITaskRepository           $taskRepository,
        private readonly ITaskDependencyRepository $taskDependencyRepository,
        private readonly IApiHttpResponder         $responder
    )
    {
    }

    public function getAllTasks(array $filters): JsonResponse
    {
        try {
            $user = auth()->user();
            $isManager = $user->can('task:view-all');

            $assignedUserId = $isManager
                ? ($filters['assigned_user_id'] ?? null)
                : $user->id;

            $status = isset($filters['status']) ? TaskStatus::tryFrom($filters['status']) : null;

            $tasks = $this->taskRepository->getFilteredTasks(
                status: $status,
                dueDateFrom: $filters['due_date_from'] ?? null,
                dueDateTo: $filters['due_date_to'] ?? null,
                assignedUserId: $assignedUserId
            );

            return $this->responder->sendSuccess(
                TaskResource::collection($tasks),
                'Tasks retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->responder->sendError(
                'Failed to retrieve tasks',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getTask(int $id): JsonResponse
    {
        try {
            $task = $this->taskRepository->getTaskWithDependencies($id);

            if (!$task) {
                return $this->responder->sendError(
                    'Task not found',
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->responder->sendSuccess(
                new TaskResource($task),
                'Task retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->responder->sendError(
                'Failed to retrieve task',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function createTask(array $data): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data['created_by'] = auth()->id();
            $data['status'] = TaskStatus::PENDING->value;

            //Create
            $task = $this->taskRepository->create($data);
            DB::commit();

            return $this->responder->sendSuccess(
                new TaskResource($task->load(['assignedUser', 'createdBy'])),
                'Task created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responder->sendError(
                'Failed to create task',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function updateTask(array $data): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validation = $this->validateTaskCompletion($data['id'], $data['status'] ?? null);
            if ($validation) {
                DB::rollBack();
                return $validation;
            }

            $task = $this->taskRepository->update(['id' => $data['id']], $data);

            DB::commit();

            return $this->responder->sendSuccess(
                new TaskResource($task->load(['assignedUser', 'createdBy', 'dependencies', 'dependents'])),
                'Task updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responder->sendError('Failed to update task', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateTaskCompletion(int $id, ?string $newStatus): ?JsonResponse
    {
        return ($newStatus === TaskStatus::COMPLETED->value && $this->taskDependencyRepository->hasIncompleteDependencies($id))
            ? $this->responder->sendError(
                'Blocking Dependencies: Prerequisite tasks must be COMPLETED before this task can be finished.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            )
            : null;
    }

    public function updateTaskStatus(array $data): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validation = $this->validateTaskCompletion($data['id'], $data['status']);
            if ($validation) {
                DB::rollBack();
                return $validation;
            }

            $task = $this->taskRepository->update(['id' => $data['id']], ['status' => $data['status']]);

            DB::commit();

            return $this->responder->sendSuccess(
                new TaskResource($task->load(['assignedUser', 'createdBy'])),
                'Task status updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responder->sendError('Failed to update task status', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * HI here I am working to handle all scenarios to prevent errors.
     * @param array $data
     * @return JsonResponse
     */
    public function addDependency(array $data): JsonResponse
    {
        try {
            $taskId = $data['id'];
            $dependsOnTaskId = $data['depends_on_task_id'];

            if ($taskId === $dependsOnTaskId) {
                return $this->responder->sendError('A task cannot depend on itself', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($this->taskDependencyRepository->dependencyExists($taskId, $dependsOnTaskId)) {
                return $this->responder->sendError('This dependency relationship already exists', Response::HTTP_CONFLICT);
            }

            $this->taskDependencyRepository->addDependency($taskId, $dependsOnTaskId);
            $task = $this->taskRepository->getTaskWithDependencies($taskId);

            return $this->responder->sendSuccess(
                new TaskResource($task),
                'Dependency mapped successfully'
            );
        } catch (\Exception $e) {
            return $this->responder->sendError('Failed to map dependency', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
