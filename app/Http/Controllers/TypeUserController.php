<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Models\TypeUser;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TypeUserRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="type-users",
 *     description="Controle dos tipos de usuário: apenas usuários com o tipo de usuário ADMINISTRADOR tem acesso a esses endpoints"
 * )
 */
class TypeUserController extends Controller
{
    public function __construct(private TypeUserRepositoryInterface $typeUsersRepository)
    {
    }

    /**
     * @OA\Post(
     *   path="/type-users",
     *   tags={"type-users"},
     *   summary="Criar novo tipo de usuário",
     *   description="Cria novo tipo de usuário",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                  "name": "Nome do usuário",
     *              }
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response="422",
     *     description="Erro de validação"
     *   ),
     *   @OA\Response(
     *     response="403",
     *     description="Unauthorized"
     *   )
     * )
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
     * @OA\Get(
     *   path="/type-users/{id}",
     *   tags={"type-users"},
     *   summary="Lista o registro de tipos de usuários por ID",
     *   description="Lista o registro de tipos de usuários por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do tipo de usuário",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Tipo de usuário not found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Unauthorized"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $typeUser = $this->typeUsersRepository->findById($id);
        return response()->json($typeUser, 200);
    }

    /**
     * @OA\Put(
     *   path="/type-users/{id}",
     *   tags={"type-users"},
     *   summary="Atualizar tipo de usuário",
     *   description="Atualizar tipo de usuário",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do tipo de usuário",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                  "name": "Nome do tipo de usuário",
     *              }
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Unauthorized"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Usuário not found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function update(string $id, TypeUserRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $payload = $request->validated();
        $typeUser = $this->typeUsersRepository->update($id, $payload);
        return response()->json($typeUser);
    }

    /**
     * @OA\Delete(
     *   path="/type-users/{id}",
     *   tags={"type-users"},
     *   summary="Deletar tipo de usuário",
     *   description="Deletar tipo de usuário por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do tipo de usuário",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=204,
     *     description="No Content"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Unauthorized"
     *   ),
     *  @OA\Response(
     *     response=404,
     *     description="Tipo de usuário Not Found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $this->typeUsersRepository->delete($id);
        return response()->json([], 204);
    }

    /**
     * @OA\Get(
     *   path="/type-users",
     *   tags={"type-users"},
     *   summary="Listar todos os tipos de usuários",
     *   description="Lista todos os tipos de usuários",
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response="403",
     *     description="Unauthorized"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, TypeUser::class);

        $typeUser = $this->typeUsersRepository->listAll();
        return response()->json($typeUser);
    }
}
