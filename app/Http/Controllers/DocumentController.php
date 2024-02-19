<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Services\DocumentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @OA\Tag(
 *     name="documents",
 *     description="CRUD dos documentos, apenas os usuários do tipo ADMINISTRADOR e REPRESENTANTE podem criar, atualizar e editar documentos"
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
     *   path="/groups/{groupId}/documents",
     *   tags={"documents"},
     *   summary="Listar todos os documentos",
     *   description="Lista todos os documentos: ADMINISTRADOR, REPRESENTANTE E GERENTE têm acesso a este endpoint.",
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
        $documents = $this->documentService->listAll($groupId);

        return response()->json($documents);
    }

    /**
     * @OA\Get(
     *   path="/groups/{groupId}/documents/{id}",
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
     *     description="Documento not found"
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
    public function show(string $groupId, string $id): JsonResponse
    {
        $document = $this->documentRepository->findById($id);

        return response()->json($document);
    }

    /**
     * @OA\Post(
     *   path="/groups/{groupId}/documents",
     *   tags={"documents"},
     *   summary="Criar novo documento",
     *   description="Cria um novo documento, somente o ADMINISTRADOR e o REPRESENTANTE tem acesso a este endpoint.",
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
     * @OA\Delete(
     *   path="/groups/{groupId}/documents/{documentId}",
     *   tags={"documents"},
     *   summary="Deletar documento",
     *   description="Deletar documento por ID de referência, somente o ADMINISTRADOR e o REPRESENTANTE tem acesso a este endpoint.",
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

    /**
     * @OA\Get(
     *   path="/groups/{groupId}/documents/{id}/download",
     *   tags={"documents"},
     *   summary="Faz download do documento",
     *   description="faz download do documento por ID",
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
     *     description="Documento not found"
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
    public function download(string $groupId, string $documentId): BinaryFileResponse|JsonResponse
    {
        $document = $this->documentRepository->findById($documentId);
        $filePath = $document->file;
        $fileName = substr($filePath, 7);

        if (Storage::disk('local')->exists($filePath)) {
            return Response::download(storage_path("app/{$filePath}"), $fileName);
        }

        return response()->json(['error' => 'Arquivo não encontrado'], 404);
    }
}
