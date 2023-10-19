<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\TypeGroupRequest;
use App\Models\TypeGroup;
use App\Repositories\Interfaces\TypeGroupRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="type-group",
 *     description="Controle dos tipos de grupo: apenas usuários com o tipo de usuário GERENTE tem acesso a esses endpoints"
 * )
 */
class TypeGroupController extends Controller
{
    public function __construct(private TypeGroupRepositoryInterface $typeGroupsRepository)
    {
    }

    /**
     * @OA\Get(
     *   path="/type-group",
     *   tags={"type-group"},
     *   summary="Listar todos os tipos de grupos",
     *   description="Lista todos os tipos de grupos",
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
        $this->authorize(AbilitiesEnum::VIEW, TypeGroup::class);

        $typeGroup = $this->typeGroupsRepository->listAll();
        return response()->json($typeGroup, 200);
    }

    /**
     * @OA\Post(
     *   path="/type-group",
     *   tags={"type-group"},
     *   summary="Criar novo tipo de grupo",
     *   description="Cria novo tipo de grupo",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                  "name": "Nome do grupo",
     *                  "type_group": "Tipo de grupo: Interno ou Externo"
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
    public function store(TypeGroupRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, TypeGroup::class);

        $payload = $request->validated();
        $typeGroup = $this->typeGroupsRepository->create($payload);
        return response()->json($typeGroup, 201);
    }

    /**
     * @OA\Get(
     *   path="/type-group/{id}",
     *   tags={"type-group"},
     *   summary="Lista o registro de tipos de grupos por ID",
     *   description="Lista o registro de tipos de grupos por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do tipo de grupo",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Tipo de grupo not found"
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
        $this->authorize(AbilitiesEnum::VIEW, TypeGroup::class);

        $typeGroup = $this->typeGroupsRepository->findById($id);
        return response()->json($typeGroup, 200);
    }

    /**
     * @OA\Put(
     *   path="/type-group/{id}",
     *   tags={"type-group"},
     *   summary="Atualizar tipo de grupo",
     *   description="Atualizar tipo de grupo",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do tipo de grupo",
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
     *                  "name": "Nome do tipo de grupo",
     *                  "type_group": "Tipo de grupo: Interno ou Externo"
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
    public function update(string $id, TypeGroupRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, TypeGroup::class);

        $payload = $request->validated();
        $typeGroup = $this->typeGroupsRepository->update($id, $payload);
        return response()->json($typeGroup);
    }

    /**
     * @OA\Delete(
     *   path="/type-group/{id}",
     *   tags={"type-group"},
     *   summary="Deletar tipo de grupo",
     *   description="Deletar tipo de grupo por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do tipo de grupo",
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
     *     description="Tipo de grupo Not Found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::DELETE, TypeGroup::class);

        $this->typeGroupsRepository->delete($id);
        return response()->json([], 204);
    }
}
