<?php

namespace App\Architecture\Responder;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

interface IApiHttpResponder
{
    /**
     * Send a success response
     *
     * @param array $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    public function sendSuccess(mixed $data = [], ?string $message = null, int $code = Response::HTTP_OK): JsonResponse;

    /**
     * Send an error response
     *
     * @param string|null $message
     * @param int $code
     * @param array $data
     * @param \Exception|null $e
     * @return JsonResponse
     */
    public function sendError(?string $message = null, int $code = Response::HTTP_NOT_FOUND, array $data = [], ?\Exception $e = null): JsonResponse;

    /**
     * Send a validation error response
     *
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    public function sendValidationError(string $message, array $errors = []): JsonResponse;
}
