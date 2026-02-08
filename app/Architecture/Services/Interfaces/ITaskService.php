<?php

namespace App\Architecture\Services\Interfaces;

use Illuminate\Http\JsonResponse;

interface ITaskService
{
    public function getAllTasks(array $filters): JsonResponse;

    public function getTask(int $id): JsonResponse;

    public function createTask(array $data): JsonResponse;

    public function updateTask(array $data): JsonResponse;

    public function updateTaskStatus(array $data): JsonResponse;

    public function addDependency(array $data): JsonResponse;
}
