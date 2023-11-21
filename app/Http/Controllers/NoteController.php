<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\UserRequest;
use App\Models\Note;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Transformer\UserTransformer;
use http\Env\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="note",
 *     description="CRUD de notas
 * )
 */
class NoteController extends Controller
{
    //Lista de notas para grupo especificado
    public function index(int $groupId): JsonResponse
    {
        $note = Note::where('group_id', $groupId)->paginate(10);

        return response()->json($note);

    }

    //Retorna nota especifica
    public function show(int $groupId, int $id): JsonResponse
    {

        $note = Note::where('group_id', $groupId)->findOrFail($id);

        return response()->json($note);
    }

    //Cria uma nova nota
    public function store(int $groupId, NoteRequest $request): JsonResponse
    {
        $this->authorize('create', Note::class);

        $note = new Note();
        $note->fill($request->all());
        $note->group_id = $groupId;
        $note->save();

        return response()->json($note, 201);
    }

    //Update

    public function update(int $groupId, int $id, NoteRequest $request): JsonResponse
    {
        $this->authorize('update', Note::class);

        $note = Note::where('group_id', $groupId)->findOrFail($id);
        $note->fill($request->all());
        $note->save();

        return response()->json($note);
    }

    //Delete
    public function destroy(int $groupId, int $id): JsonResponse
    {
        $this->authorize('delete', Note::class);

        $note = Note::where('group_id', $groupId)->findOrFail($id);
        $note->delete();

        return response()->json([], 204);
    }
    }




