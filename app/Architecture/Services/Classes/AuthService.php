<?php

namespace App\Architecture\Services\Classes;

use App\Architecture\Repositories\Interfaces\IUserRepository;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Interfaces\IAuthService;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService implements IAuthService
{
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly IApiHttpResponder $responder
    ) {
    }

    public function login(array $credentials): JsonResponse
    {
        try {
            $user = $this->userRepository->findByEmail($credentials['email']);

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return $this->responder->sendError('Invalid credentials', Response::HTTP_UNAUTHORIZED);
            }

            $user->tokens()->delete();
            // Create token with ability to access everything, relying on Spatie for restrictions
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->responder->sendSuccess([
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');

        } catch (\Exception $e) {
            return $this->responder->sendError('Login failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $user = auth()->user();

            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            return $this->responder->sendSuccess([], 'Logged out successfully');

        } catch (\Exception $e) {
            return $this->responder->sendError('Logout failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
