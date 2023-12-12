<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use App\Repositories\Interfaces\ActivityRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="activity",
 *     description="CRUD das atividades, apenas usuários do tipo REPRESENTANTES podem criar, atualizar e editar atividades"
 * )
 */
class ActivityController extends Controller
{
    public function __construct(
        private ActivityRepositoryInterface $activityRepository,
        private GroupRepositoryInterface $groupRepository
    ) {
    }

    /**
     * @OA\Get(
     *   path="/group/{groupId}/activity",
     *   tags={"activity"},
     *   summary="Listar todos as atividades",
     *   description="Lista todas as atividades: ADMINISTRADOR, REPRESENTANTE E GERENTE têm acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="O ID do grupo",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Erro"
     *   ),
     *   @OA\Response(
     *     response="403",
     *     description="Não autorizado"
     *   )
     * )
     */
    public function index(string $groupId): JsonResponse
    {
        $group = $this->groupRepository->findById($groupId);
        $activities = $group->activity;

        return response()->json($activities);
    }

    /**
     * @OA\Get(
     *   path="/activity/{id}",
     *   tags={"activity"},
     *   summary="Lista o registro de atividades por ID",
     *   description="Lista o registro de atividades por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id da atividade",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Atividade not found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Não autorizado"
     *   )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $activity = $this->activityRepository->findById($id);

        return response()->json($activity);
    }

    /**
     * @OA\Post(
     *   path="/group/{groupId}/activity",
     *   tags={"activity"},
     *   summary="Criar nova atividade",
     *   description="Cria uma nova atividade, somente o REPRESENTANTE tem acesso a este endpoint.",
     *  @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="Id do grupo que o documento sera associado",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *          @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="nome da atividade",
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="descrição da atividade",
     *              ),
     *         )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Criado"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Erro"
     *   ),
     *   @OA\Response(
     *     response="422",
     *     description="Erro de validação"
     *   ),
     *   @OA\Response(
     *     response="403",
     *     description="Não autorizado"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function store(ActivityRequest $request, string $groupId): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Activity::class, $groupId]);
        $this->groupRepository->findById($groupId);
        $payload = array_merge($request->all(), ['group_id' => $groupId]);
        $activity = $this->activityRepository->create($payload);
        return response()->json($activity, 201);
    }

    /**
     * @OA\Put(
     *   path="/activity/{id}",
     *   tags={"activity"},
     *   summary="Atualiza atividades",
     *   description="Atualizar documentos: somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id da atividade",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *       mediaType="application/json",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="nome da atividade",
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="descrição da atividade",
     *              ),
     *         )
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Erro"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Não autorizado"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Atividade not found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function update(string $id, ActivityRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [Activity::class, $id]);
        $activity = $this->activityRepository->update($id, $request->all());
        return response()->json($activity);
    }

    /**
     * @OA\Delete(
     *   path="/group/{groupId}/activity/{activityId}",
     *   tags={"activity"},
     *   summary="Deletar atividade",
     *   description="Deletar atividade por ID de referência, somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="Id do grupo",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="activityId",
     *     in="path",
     *     description="Id da atividade",
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
     *     description="Erro"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Não autorizado"
     *   ),
     *)
     * @throws AuthorizationException
     */
    public function destroy(string $groupId, string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Activity::class, $groupId]);
        $this->groupRepository->findById($groupId);
        $this->activityRepository->delete($id);
        return response()->json([], 204);
    }
}
