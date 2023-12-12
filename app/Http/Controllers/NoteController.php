<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\ActivityRequest;
use App\Http\Requests\NoteRequest;
use App\Models\Activity;
use App\Models\Note;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="notes",
 *     description="CRUD das notas, apenas usuários do tipo REPRESENTANTES podem criar, atualizar e editar atividades"
 * )
 */
class NoteController extends Controller
{
    public function __construct(
        private NoteRepositoryInterface $noteRepository,
        private GroupRepositoryInterface $groupRepository
    ) {
    }

    /**
     * @OA\Get(
     *   path="/notes",
     *   tags={"notes"},
     *   summary="Listar todos as notas",
     *   description="Lista todas as notas: ADMINISTRADOR, REPRESENTANTE E GERENTE têm acesso a este endpoint.",
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
    public function index(): JsonResponse
    {
        $notes = $this->noteRepository->listAll();

        return response()->json($notes);
    }

    /**
     * @OA\Get(
     *   path="/notas/{id}",
     *   tags={"notas"},
     *   summary="Lista o registro de notas por ID",
     *   description="Lista o registro de notas por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id da nota",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Note not found"
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
        $note = $this->noteRepository->findById($id);

        return response()->json($note);
    }

    /**
     * @OA\Post(
     *   path="/group/{groupId}/notas",
     *   tags={"notas"},
     *   summary="Criar nova nota",
     *   description="Cria uma nova nota, somente o REPRESENTANTE tem acesso a este endpoint.",
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
     *                  property="title",
     *                  type="string",
     *                  description="titulo da nota",
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="descrição da nota",
     *              ),
     *              @OA\Property(
     *                  property="color",
     *                  type="string",
     *                  description="cor da nota desejada",
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
    public function store(NoteRequest $request, string $groupId): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Note::class, $groupId]);
        $this->groupRepository->findById($groupId);
        $payload = array_merge($request->all(), ['group_id' => $groupId]);
        $note = $this->noteRepository->create($payload);
        return response()->json($note, 201);
    }

    /**
     * @OA\Put(
     *   path="/notes/{id}",
     *   tags={"notes"},
     *   summary="Atualiza notas",
     *   description="Atualizar notas: somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id da nota",
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
     *                  property="title",
     *                  type="string",
     *                  description="titulo da nota",
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="descrição da nota",
     *              ),
     *              @OA\Property(
     *                  property="color",
     *                  type="string",
     *                  description="cor da nota desejada",
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
     *     description="Note not found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function update(string $id, NoteRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [Note::class, $id]);
        $note = $this->noteRepository->update($id, $request->all());
        return response()->json($note);
    }

    /**
     * @OA\Delete(
     *   path="/group/{groupId}/notes/{noteId}",
     *   tags={"notes"},
     *   summary="Deletar notas",
     *   description="Deletar nota por ID de referência, somente o REPRESENTANTE tem acesso a este endpoint.",
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
     *     name="noteId",
     *     in="path",
     *     description="Id da nota",
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
        $this->noteRepository->delete($id);
        return response()->json([], 204);
    }
}
