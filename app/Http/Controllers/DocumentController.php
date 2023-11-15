<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Services\DocumentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentRepositoryInterface $documentRepository,
        private DocumentService $documentService
    )
    {
    }

    public function index(): JsonResponse
    {
        $documents = $this->documentRepository->listAll();

        return response()->json($documents);
    }

    public function show(string $id): JsonResponse
    {
        $document = $this->documentRepository->findById($id);

        return response()->json($document);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(DocumentRequest $request, string $groupId): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Document::class, $groupId]);
        $document = $this->documentService->create($groupId, $request->all());
        return response()->json($document, 201);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(string $id, DocumentRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [Document::class, $id]);
        $document = $this->documentService->edit($id, $request->all());
        return response()->json($document);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(string $groupId, string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Document::class, $groupId]);
        $this->documentService->delete($groupId, $id);
        return response()->json([], 204);
    }
}
