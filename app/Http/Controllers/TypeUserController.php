<?php

namespace App\Http\Controllers;

use App\Repositories\TypeUserRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTypeUserRequest;
use App\Http\Requests\UpdateTypeUserRequest;

class TypeUserController extends Controller
{
    public function __construct(private TypeUserRepository $typeUsersRepository)
    {
    }

    public function store(StoreTypeUserRequest $request):JsonResponse
    {
        $payload= $request->validated();
        $typeUser=$this->typeUsersRepository->create($payload);
        return response()->json($typeUser, 201);
    }
    public function show(string $id): JsonResponse
    {
        $typeUser=$this->typeUsersRepository->findById($id);
        return response()->json($typeUser, 200);
    }

    public function update(string $id, UpdateTypeUserRequest $request):JsonResponse
    {
        $payload= $request->validated();
        $typeUser=$this->typeUsersRepository->update($id, $payload);
        return response()->json($typeUser, 201);
    }
    public function destroy(string $id):JsonResponse
    {
        $this->typeUsersRepository->delete($id);
        return response()->json([], 204);
    }
    public function index():JsonResponse
    {
        $typeUser=$this->typeUsersRepository->listAll();
        return response()->json($typeUser, 200);
    }
}
