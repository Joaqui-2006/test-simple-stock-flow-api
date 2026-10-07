<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\Authenticate;
use App\Presentation\Http\Request\LoginRequest;
use App\Presentation\Http\Request\RegisterRequest;
use Illuminate\Http\JsonResponse;

final class AuthController
{
    public function __construct(
        private readonly Authenticate $authService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            (string) $request->validated('username'),
            (string) $request->validated('password')
        );

        return response()->json([
            'token' => $result->token,
            'user' => [
                'id' => $result->userId,
                'username' => $result->username,
                'role' => $result->role,
            ]
        ], 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $seller = $this->authService->registerSeller(
            (string) $request->validated('username'),
            (string) $request->validated('password')
        );

        return response()->json($seller, 201);
    }
}