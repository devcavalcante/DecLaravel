<?php

namespace App\Http\Controllers;

use App\Repositories\TypeGroupsRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TypeUserRequest;
use App\Http\Requests\UpdateTypeUserRequest;

class TypeGroupController extends Controller
{
    public function __construct(private TypeGroupsRepository $typeGroupsRepository)
    {
    }

    public function store(TypeUserRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $typeGroup = $this->typeGroupsRepository->create($payload);
        return response()->json($typeGroup, 201);
    }

    public function show(string $id): JsonResponse
    {
        $typeGroup = $this->typeGroupsRepository->findById($id);
        return response()->json($typeGroup, 200);
    }

    public function update(string $id, UpdateTypeUserRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $typeGroup = $this->typeGroupsRepository->update($id, $payload);
        return response()->json($typeGroup, 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->typeGroupsRepository->delete($id);
        return response()->json([], 204);
    }

    public function index():JsonResponse
    {
        $typeUser = $this->typeGroupsRepository->listAll();
        return response()->json($typeUser, 200);
    }
}
