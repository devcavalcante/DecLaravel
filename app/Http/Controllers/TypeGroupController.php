<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\TypeGroupRequest;
use App\Models\TypeGroup;
use App\Repositories\TypeGroupRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TypeUserRequest;

class TypeGroupController extends Controller
{
    public function __construct(private TypeGroupRepository $typeGroupsRepository)
    {
    }

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeGroup::class);

        $typeUser = $this->typeGroupsRepository->listAll();
        return response()->json($typeUser, 200);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(TypeGroupRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, TypeGroup::class);

        $payload = $request->validated();
        $typeGroup = $this->typeGroupsRepository->create($payload);
        return response()->json($typeGroup, 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeGroup::class);

        $typeGroup = $this->typeGroupsRepository->findById($id);
        return response()->json($typeGroup, 200);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(string $id, TypeGroupRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, TypeGroup::class);

        $payload = $request->validated();
        $typeGroup = $this->typeGroupsRepository->update($id, $payload);
        return response()->json($typeGroup, 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::DELETE, TypeGroup::class);

        $this->typeGroupsRepository->delete($id);
        return response()->json([], 204);
    }
}
