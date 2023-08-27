<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthorizedException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Throwable;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * @throws Throwable
     */
    public function register(UserRequest $userRequest): JsonResponse
    {
        $data = $userRequest->all();
        $user = $this->authService->register($data);
        return response()->json($user, 201);
    }

    /**
     * @throws AuthorizedException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->all();
        $user = $this->authService->login($data);
        return response()->json($user);
    }

    /**
     * @throws AuthorizedException
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json([], 204);
    }
}
