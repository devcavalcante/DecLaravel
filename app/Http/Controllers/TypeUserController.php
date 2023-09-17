<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Models\TypeUser;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TypeUserRequest;

class TypeUserController extends Controller
{
    public function __construct(private TypeUserRepositoryInterface $typeUsersRepository)
    {
    }

    /**
     * @throws AuthorizationException
     */
    public function store(TypeUserRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $payload = $request->validated();
        $typeUser = $this->typeUsersRepository->create($payload);
        return response()->json($typeUser, 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $typeUser = $this->typeUsersRepository->findById($id);
        return response()->json($typeUser, 200);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(string $id, TypeUserRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $payload = $request->validated();
        $typeUser = $this->typeUsersRepository->update($id, $payload);
        return response()->json($typeUser, 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $this->typeUsersRepository->delete($id);
        return response()->json([], 204);
    }

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $typeUser = $this->typeUsersRepository->listAll();
        return response()->json($typeUser);
    }
}
