<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\MeetingRequest;
use App\Models\Meeting;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MeetingRepositoryInterface;
use App\Services\MeetingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        private MeetingService $meetingService,
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
    public function index(string $groupId): JsonResponse
    {
        $meetings = $this->meetingService->listAll($groupId);
        return response()->json($meetings);
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
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="content",
     *                  type="string",
     *                  description="conteudo da reuniao",
     *              ),
     *              @OA\Property(
     *                  property="summary",
     *                  type="string",
     *                  description="sumario da reuniao",
     *              ),
     *              @OA\Property(
     *                  description="ata",
     *                  property="file",
     *                  type="file",
     *                  description="ata da reunião"
     *             ),
     *              @OA\Property(
     *                  description="date_meet",
     *                  type="file",
     *                  description="data da reunião"
     *              ),
     *         )
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
        $meeting = $this->meetingService->create($groupId, $request->all());
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
     *   path="/group/{groupId}/meeting-history/{id}",
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
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="content",
     *                  type="string",
     *                  description="conteudo da reuniao",
     *              ),
     *              @OA\Property(
     *                  property="summary",
     *                  type="string",
     *                  description="sumario da reuniao",
     *              ),
     *              @OA\Property(
     *                  description="ata",
     *                  property="file",
     *                  type="file",
     *                  description="ata da reunião"
     *             ),
     *              @OA\Property(
     *                  description="date_meet",
     *                  type="file",
     *                  description="data da reunião"
     *              ),
     *         )
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
        $meeting = $this->meetingService->edit($id, $request->all());
        return response()->json($meeting);
    }

    /**
     * @OA\Delete(
     *   path="/group/{groupId}/meeting-history/{id}",
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

    /**
     * @OA\Get(
     *   path="/meeting-history/download/{id}",
     *   tags={"meetings"},
     *   summary="Faz download da ata",
     *   description="faz download da ata por ID",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do historico de reunião",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="histórico not found"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     * )
     */
    public function download(string $meetingId): BinaryFileResponse|JsonResponse
    {
        $document = $this->meetingRepository->findById($meetingId);
        $filePath = $document->ata;
        $fileName = substr($filePath, 5);

        if (Storage::disk('local')->exists($filePath)) {
            return Response::download(storage_path("app/{$filePath}"), $fileName);
        }

        return response()->json(['error' => 'Arquivo não encontrado'], 404);
    }
}
