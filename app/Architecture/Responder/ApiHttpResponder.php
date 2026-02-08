<?php

namespace App\Architecture\Responder;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiHttpResponder implements IApiHttpResponder
{
    /**
     * Send a success response
     */
    public function sendSuccess(mixed $data = [], ?string $message = null, int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'code' => $code,
            'message' => $message ?? 'Operation completed successfully',
            'data' => $data,
        ];

        return response()->json($response, $code);
    }

    /**
     * Send an error response
     */
    public function sendError(?string $message = null, int $code = Response::HTTP_NOT_FOUND, array $data = [], ?\Exception $e = null): JsonResponse
    {
        // Log the error for debugging
        Log::error('API Error Response', [
            'exception' => $e ? [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'type' => get_class($e),
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null,
            ] : null,
            'message' => $message,
            'http_code' => $code,
            'user_id' => auth()->id(),
            'route' => request()->path(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'data' => $data,
        ]);

        return response()->json([
            'code' => $code,
            'message' => $message ?? 'An error occurred',
            'data' => $data,
        ], $code);
    }

    /**
     * Send a validation error response
     */
    public function sendValidationError(string $message, array $errors = []): JsonResponse
    {
        return response()->json([
            'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $message,
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
