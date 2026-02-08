<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'description' => $this->whenNotNull($this->description),
            'status' => $this->status?->value ?? $this->status,
            'due_date' => $this->whenNotNull($this->due_date?->format('Y-m-d')),

            // User Info
            'assigned_user' => new UserResource($this->whenLoaded('assignedUser')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),

            // Dependencies (Tasks that THIS task depends on - Prerequisites)
            'dependencies' => $this->whenLoaded('dependencies', function () {
                return $this->dependencies->map(function ($pivot) {
                    return $pivot->dependency ? [
                        'id' => $pivot->dependency->id,
                        'title' => $pivot->dependency->title,
                        'status' => $pivot->dependency->status?->value ?? $pivot->dependency->status,
                    ] : null;
                })->filter()->values();
            }),

            // Dependents (Tasks that depend ON this task - Blockers)
            'dependents' => $this->whenLoaded('dependents', function () {
                return $this->dependents->map(function ($pivot) {
                    return $pivot->dependent ? [
                        'id' => $pivot->dependent->id,
                        'title' => $pivot->dependent->title,
                        'status' => $pivot->dependent->status?->value ?? $pivot->dependent->status,
                    ] : null;
                })->filter()->values();
            }),

            'created_at' => $this->whenNotNull($this->created_at?->toISOString()),
        ];
    }
}
