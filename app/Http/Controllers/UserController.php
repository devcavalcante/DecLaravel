<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, User::class);

        $users = $this->userRepository->listAll();
        return response()->json($users, 200);
    }


    public function show(string $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);
        return response()->json($user);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(string $id, UserRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [User::class, $id]);
        $payload = $request->validated();
        $user = $this->userRepository->update($id, $payload);
        return response()->json($user, 200);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::DELETE, User::class);
        $this->userRepository->delete($id);
        return response()->json([], 204);
    }

    /**
     * @throws AuthorizationException
     */
    public function restore($id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::RESTORE, User::class);
        $user = $this->userRepository->restore($id);
        return response()->json($user);
    }
}
