<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserControllerRequest;
use App\Models\User;

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

    public function store(UserControllerRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $user = $this->userRepository->create($payload);
        return response()->json($user, 201);
    }

    public function update(string $id, UserControllerRequest $request): JsonResponse
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
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $user->restore();
        return response()->json(['message' => 'Usuário restaurado com sucesso']);
    }
}
