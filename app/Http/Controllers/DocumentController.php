<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Services\DocumentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="documents",
 *     description="CRUD dos documentos, apenas usuários do tipo REPRESENTANTES podem criar, atualizar e editar membros"
 * )
 */
class DocumentController extends Controller
{
    public function __construct(
        private DocumentRepositoryInterface $documentRepository,
        private DocumentService $documentService
    ) {
    }

    /**
     * @OA\Get(
     *   path="/documents",
     *   tags={"documents"},
     *   summary="Listar todos os membros",
     *   description="Lista todos os membros: ADMINISTRADOR, REPRESENTANTE E GERENTE têm acesso a este endpoint.",
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
        $documents = $this->documentRepository->listAll();

        return response()->json($documents);
    }

    /**
     * @OA\Get(
     *   path="/documents/{id}",
     *   tags={"documents"},
     *   summary="Lista o registro de documento por ID",
     *   description="Lista o registro de documento por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do documento",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Membro not found"
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
        $document = $this->documentRepository->findById($id);

        return response()->json($document);
    }

    /**
     * @OA\Post(
     *   path="/group/{groupId}/documents",
     *   tags={"documents"},
     *   summary="Criar novo documento",
     *   description="Cria um novo documento, somente o REPRESENTANTE tem acesso a este endpoint.",
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
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="descrição do documento",
     *              ),
     *              @OA\Property(
     *                  description="documento a ser anexado",
     *                  property="file",
     *                  type="file",
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
    public function store(DocumentRequest $request, string $groupId): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Document::class, $groupId]);
        $document = $this->documentService->create($groupId, $request->all());
        return response()->json($document, 201);
    }

    /**
     * @OA\Post(
     *   path="/documents/{id}",
     *   tags={"documents"},
     *   summary="Atualiza documentos",
     *   description="Atualizar documentos: somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do documento",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="descrição do documento",
     *              ),
     *              @OA\Property(
     *                  description="anexo da tarefa",
     *                  property="file",
     *                  type="file",
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
     *     description="Membro not found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function update(string $id, DocumentRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [Document::class, $id]);
        $document = $this->documentService->edit($id, $request->all());
        return response()->json($document);
    }

    /**
     * @OA\Delete(
     *   path="/group/{groupId}/documents/{documentId}",
     *   tags={"documents"},
     *   summary="Deletar documento",
     *   description="Deletar documento por ID de referência, somente o REPRESENTANTE tem acesso a este endpoint.",
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
     *     name="documentId",
     *     in="path",
     *     description="Id do documento",
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
        $this->authorize(AbilitiesEnum::CREATE, [Document::class, $groupId]);
        $this->documentService->delete($groupId, $id);
        return response()->json([], 204);
    }
}
