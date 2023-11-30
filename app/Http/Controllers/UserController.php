<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use App\Transformer\UserTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="users",
 *     description="CRUD dos usuários"
 * )
 */
class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserService $userService,
    ) {
    }

    /**
     * @OA\Get(
     *   path="/users",
     *   tags={"users"},
     *   summary="Listar todos os usuários",
     *   description="Lista todos os usuários: 3 tipos de usuários obtem o acesso desse endpoint: ADMINISTRADOR, REPRESENTANTE E GERENTE",
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
    public function index(Request $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, User::class);

        $users = $this->userService->findMany($request->all());
        return response()->json($this->transform(new UserTransformer(), $users));
    }


    /**
     * @OA\Get(
     *   path="/users/{id}",
     *   tags={"users"},
     *   summary="Lista o registro de usuários por ID",
     *   description="Lista o registro de usuários por ID de referência",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do usuário",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Usuário not found"
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
        $user = $this->userRepository->findById($id);
        return response()->json($this->transform(new UserTransformer(), $user));
    }

    /**
     * @OA\Put(
     *   path="/users/{id}",
     *   tags={"users"},
     *   summary="Atualizar usuário",
     *   description="Atualizar usuário: Apenas o usuário pode atualizar suas próprias informações ou o usuário que o criou",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do usuário",
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
     *                  "name": "Nome do usuário",
     *                  "email": "Email do usuário",
     *                  "password": "Senha do usuário",
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
     */
    public function update(string $id, UserRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [User::class, $id]);
        $payload = $request->validated();
        $user = $this->userRepository->update($id, $payload);
        return response()->json($this->transform(new UserTransformer(), $user));
    }

    /**
     * @OA\Delete(
     *   path="/users/{id}",
     *   tags={"users"},
     *   summary="Deletar usuário",
     *   description="Deletar usuário por ID de referência: Apenas o usuário pode deletar suas próprias informações ou o usuário que o criou",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do usuário",
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
     *   @OA\Response(
     *     response=404,
     *     description="Usuario Not Found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::DELETE, [User::class, $id]);
        $this->userRepository->delete($id);
        return response()->json([], 204);
    }

    /**
     * @OA\Patch(
     *   path="/users/restore/{id}",
     *   tags={"users"},
     *   summary="Restaurar usuário",
     *   description="Restaurar usuário: Apenas o usuário pode restaurar suas próprias informações ou o usuário que o criou",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do usuário",
     *     required=true,
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
     *     response="404",
     *     description="Usuário not found"
     *   )
     * )
     * @throws AuthorizationException
     */
    public function restore(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::RESTORE, [User::class, $id]);
        $user = $this->userRepository->restore($id);
        return response()->json($this->transform(new UserTransformer(), $user));
    }
}
