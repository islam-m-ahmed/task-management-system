<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskDependencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $this is a TaskDependency model
        // We want to return the info of the task it depends on (or the task that depends on it)
        $task = $this->dependency ?? $this->dependent;

        if (!$task) {
            return [];
        }

        return [
            'id' => $task->id,
            'title' => $task->title,
            'status' => $task->status?->value ?? $task->status,
        ];
    }
}
