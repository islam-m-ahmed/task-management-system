<?php

namespace App\Http\Controllers\Api\V1;

use App\Architecture\Services\Interfaces\IAuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private readonly IAuthService $authService)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }
}
