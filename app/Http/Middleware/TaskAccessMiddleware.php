<?php

namespace App\Http\Middleware;

use App\Architecture\Responder\IApiHttpResponder;
use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskAccessMiddleware
{
    public function __construct(
        private readonly IApiHttpResponder $responder
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return $this->responder->sendError('Unauthenticated', Response::HTTP_UNAUTHORIZED);
        }

        $taskId = $request->route('id');
        if (!$taskId) {
            return $next($request);
        }

        $task = Task::find($taskId);
        if (!$task) {
            return $this->responder->sendError('Task not found', Response::HTTP_NOT_FOUND);
        }

        // Managers can do everything
        if ($user->can('task:view-all')) {
            return $next($request);
        }

        // Users can only access tasks assigned to them
        if ($task->assigned_user_id !== $user->id) {
            return $this->responder->sendError(
                'You do not have access to this task',
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
