<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function index(): JsonResponse
    {
        $users = $this->userRepository->listAll();
        return response()->json($users, 200);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);
        return response()->json($user, 200);
    }

    public function update(string $id, UserRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $user = $this->userRepository->update($id, $payload);
        return response()->json($user, 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->userRepository->delete($id);
        return response()->json([], 204);
    }

    public function restore($id): JsonResponse
    {
        $user = $this->userRepository->restore($id);
        return response()->json($user);
    }
}
