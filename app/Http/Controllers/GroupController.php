<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\TypeGroupRequest;
use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Services\GroupService;
use App\Transformer\GroupTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Throwable;

/**
 * @OA\Tag(
 *     name="group",
 *     description="Controle dos grupo: apenas usuários com o tipo de usuário GERENTE tem acesso aos endpoints de criação, deleção e atualizar"
 * )
 */
class GroupController extends Controller
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly GroupService $groupService
    ) {
    }

    /**
     * @OA\Get(
     *   path="/group",
     *   tags={"group"},
     *   summary="Listar todos os grupos",
     *   description="Lista todos os grupos",
     *   @OA\Parameter(
     *     name="entity",
     *     in="query",
     *     description="nome da entidade que deseja filtrar",
     *     @OA\Schema(
     *         type="string"
     *     )
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
     *     response="403",
     *     description="Unauthorized"
     *   )
     * )
     */
    public function index(GroupRequest $groupRequest): JsonResponse
    {
        $groups = $this->groupService->findMany($groupRequest->all());

        return response()->json($this->transform(new GroupTransformer(), $groups));
    }

    /**
     * @OA\Post(
     *   path="/group",
     *   tags={"group"},
     *   summary="Criar novo grupo",
     *   description="Cria novo grupo",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *             @OA\Property(
     *                 property="representatives",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={2, 4}
     *             ),
     *              example={
     *                 "entity": "exercitationem",
     *                 "organ": "architecto",
     *                 "council": "voluptates",
     *                 "acronym": "nesciunt",
     *                 "team": "ut",
     *                 "unit": "occaecati",
     *                 "email": "amanda24@hotmail.com",
     *                 "office_requested": "accusamus",
     *                 "office_indicated": "incidunt",
     *                 "internal_concierge": "corrupti",
     *                 "type_group_id": 1,
     *                 "observations": "Repellendus aut voluptatem quaerat consequuntur illum. Dolor est sed natus est. Qui voluptatibus iure necessitatibus velit.",
     *                 "representatives": {2,4},
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
     *   ),
     * @OA\Response(
     *     response="400",
     *     description="Apenas usuarios do tipo representante sao permitidos"
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
        return response()->json($this->transform(new GroupTransformer(), $group), 201);
    }

    /**
     * @OA\Get(
     *   path="/group/{id}",
     *   tags={"group"},
     *   summary="Lista o registro de grupos por ID",
     *   description="Lista o registro de grupos por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do grupo",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="grupo not found"
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
     */
    public function show(string $id): JsonResponse
    {
        $group = $this->groupRepository->findById($id);
        return response()->json($this->transform(new GroupTransformer(), $group));
    }

    /**
     * @OA\Put(
     *   path="/group/{id}",
     *   tags={"group"},
     *   summary="Atualizar grupo",
     *   description="Atualizar grupo",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do grupo",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *                 @OA\Property(
     *                 property="representatives",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={2, 4}
     *             ),
     *              example={
     *                 "entity": "exercitationem",
     *                 "organ": "architecto",
     *                 "council": "voluptates",
     *                 "acronym": "nesciunt",
     *                 "team": "ut",
     *                 "unit": "occaecati",
     *                 "email": "amanda24@hotmail.com",
     *                 "office_requested": "accusamus",
     *                 "office_indicated": "incidunt",
     *                 "internal_concierge": "corrupti",
     *                 "type_group_id": 1,
     *                 "observations": "Repellendus aut voluptatem quaerat consequuntur illum. Dolor est sed natus est. Qui voluptatibus iure necessitatibus velit.",
     *                 "representatives": {2,4},
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
     *   ),
     *   @OA\Response(
     *     response="400",
     *     description="Apenas usuarios do tipo representante sao permitidos"
     *   )
     * )
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(string $id, TypeGroupRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [Group::class, $id]);

        $payload = $request->all();
        $group = $this->groupService->edit($id, $payload);
        return response()->json($this->transform(new GroupTransformer(), $group));
    }

    /**
     * @OA\Delete(
     *   path="/group/{id}",
     *   tags={"group"},
     *   summary="Deletar grupo",
     *   description="Deletargrupo por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do grupo",
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
     *     description="grupo Not Found"
     *   )
     * )
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::DELETE, [Group::class, $id]);

        $this->groupService->delete($id);
        return response()->json([], 204);
    }
}
