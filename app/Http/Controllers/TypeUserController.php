<?php

namespace App\Http\Controllers;

use App\Repositories\TypeUsersRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTypeUserRequest;
<<<<<<< HEAD
use App\Http\Requests\UpdateTypeUserRequest;
=======
>>>>>>> origin/DEC-45-backend-crud-tipos-de-usuarios

class TypeUserController extends Controller
{
    public function __construct(private TypeUsersRepository $typeUsersRepository)
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
<<<<<<< HEAD
    public function update(string $id, UpdateTypeUserRequest $request ):JsonResponse
=======
    public function update(string $id, \Illuminate\Http\Request $request):JsonResponse
>>>>>>> origin/DEC-45-backend-crud-tipos-de-usuarios
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
