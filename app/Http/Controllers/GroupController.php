<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\TypeGroupRequest;
use App\Models\Group;
use App\Models\TypeGroup;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\TypeGroupRepositoryInterface;
use App\Services\GroupService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Throwable;

/**
 * @OA\Tag(
 *     name="type-group",
 *     description="Controle dos tipos de grupo: apenas usuários com o tipo de usuário GERENTE tem acesso a esses endpoints"
 * )
 */
class GroupController extends Controller
{
    public function __construct(private readonly GroupRepositoryInterface $groupRepository, private  GroupService $groupService)
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
        $this->authorize(AbilitiesEnum::VIEW, Group::class);

        $groups = $this->groupRepository->listAll();
        return response()->json($groups);
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
     * @throws Throwable
     */
    public function store(GroupRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, Group::class);

        $payload = $request->all();
        $group = $this->groupService->create($payload);
        return response()->json($group, 201);
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

        $group = $this->groupRepository->findById($id);
        return response()->json($group);
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
     * @throws Throwable
     */
    public function update(string $id, TypeGroupRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, Group::class);

        $payload = $request->all();
        $group = $this->groupService->edit($id, $payload);
        return response()->json($group);
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
     * @throws Throwable
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::DELETE, Group::class);

        $this->groupService->delete($id);
        return response()->json([], 204);
    }
}
