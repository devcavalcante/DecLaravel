<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\MeetingRequest;
use App\Models\Meeting;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MeetingRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="meetings",
 *     description="CRUD das reuniõe, apenas usuários do tipo REPRESENTANTES podem criar, atualizar e editar historico de reuniões"
 * )
 */
class MeetingController extends Controller
{
    public function __construct(
        private MeetingRepositoryInterface $meetingRepository,
        private GroupRepositoryInterface $groupRepository,
    ) {
    }

    /**
     * @OA\Get(
     *   path="/group/{groupId}/meeting-history",
     *   tags={"meetings"},
     *   summary="Listar todos os históricos de reuniões do grupo",
     *   description="Lista todos os históricos de reuniões do grupo especificado",
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
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response="403",
     *     description="Unauthorized"
     *   )
     * )
     */
    public function index(): JsonResponse
    {
        $meetings = $this->meetingRepository->listAll();
        return response()->json($meetings, 200);
    }

    /**
     * @OA\Post(
     *   path="/group/{groupId}/meeting-history",
     *   tags={"meetings"},
     *   summary="Criar um novo histórico de reunião para o grupo especificado",
     *   description="Cria um novo histórico de reunião para o grupo especificado: somente o REPRESENTANTE que estiver ligado ao grupo tem acesso desse endpoint",
     *   @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="O ID do grupo",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         type="object",
     *         required={"content", "summary", "ata"},
     *         @OA\Property(
     *           property="content",
     *           description="O conteúdo da reunião",
     *           type="string",
     *           minLength=5
     *         ),
     *         @OA\Property(
     *           property="summary",
     *           description="O resumo da reunião",
     *           type="string",
     *           minLength=5
     *         ),
     *         @OA\Property(
     *           property="ata",
     *           description="A ata da reunião",
     *           type="string",
     *           minLength=5),
     *         @OA\Property(
     *           property="date_meet",
     *           description="Data da reunião",
     *           type="date"
     *         )
     *       )
     *     )
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
     *     response="403",
     *     description="Unauthorized"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function store(MeetingRequest $request, string $groupId): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Meeting::class, $groupId]);
        $payload = $request->validated();
        $this->groupRepository->findById($groupId);
        $payload = array_merge($payload, ['group_id' => $groupId]);
        $meeting = $this->meetingRepository->create($payload);
        return response()->json($meeting, 201);
    }

    /**
     * @OA\Get(
     *   path="/group/{groupId}/meeting-history/{id}",
     *   tags={"meetings"},
     *   summary="Exibir o histórico de uma reunião",
     *   description="Exibe uma reunião no grupo especificado",
     *   @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     required=true,
     *     description="O ID do grupo",
     *   ),
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="O ID da reunião.",
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok",
     *   ),
     *   @OA\Response(
     *     response="403",
     *     description="Unauthorized"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Not Found"
     *   )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $meeting = $this->meetingRepository->findById($id);
        return response()->json($meeting);
    }

    /**
     * @OA\Put(
     *   path="/group/{groupId}/meeting-history",
     *   tags={"meetings"},
     *   summary="Atualizar o histórico de reunião para o grupo especificado",
     *   description="Atualiza o histórico de reunião para o grupo especificado: somente o REPRESENTANTE que estiver ligado ao grupo tem acesso desse endpoint",
     *   @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="O ID do grupo",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         type="object",
     *         required={"content", "summary", "ata", "date_meet"},
     *         @OA\Property(
     *           property="content",
     *           description="O conteúdo da reunião",
     *           type="string",
     *           minLength=5
     *         ),
     *         @OA\Property(
     *           property="summary",
     *           description="O resumo da reunião",
     *           type="string",
     *           minLength=5
     *         ),
     *         @OA\Property(
     *           property="ata",
     *           description="A ata da reunião",
     *           type="string",
     *           minLength=5),
     *         @OA\Property(
     *           property="date_meet",
     *           description="Data da reunião",
     *           type="date"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Updated"
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
    public function update(string $id, MeetingRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [Meeting::class, $id]);
        $payload = $request->validated();
        $meeting = $this->meetingRepository->update($id, $payload);
        return response()->json($meeting, 200);
    }

    /**
     * @OA\Delete(
     *   path="/group/{groupId}/meeting-history",
     *   tags={"meetings"},
     *   summary="Excluir histórico de reunião",
     *   description="Excluir histórico de reunião para o grupo especificado: somente o REPRESENTANTE que estiver ligado ao grupo tem acesso desse endpoint",
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
     *     response=204,
     *     description="Deleted"
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
    public function destroy(string $groupId, string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Meeting::class, $groupId]);
        $this->groupRepository->findById($groupId);
        $this->meetingRepository->delete($id);
        return response()->json([], 204);
    }
}
