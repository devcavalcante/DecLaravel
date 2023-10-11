<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Members",
 *     description="CRUD dos membros"
 * )
 */
class MemberController extends Controller
{
    public function __construct(private MemberRepositoryInterface $memberRepository)
    {
    }

    /**
     * @OA\Get(
     *   path="/members",
     *   tags={"members"},
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
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize(AbilitiesEnum::VIEW, Member::class);

        $members = $this->memberRepository->listWithUsers();
        return response()->json($members, 200);
    }

    /**
     * @OA\Post(
     *   path="/members",
     *   tags={"members"},
     *   summary="Criar novo membro",
     *   description="Cria um novo membro, somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              required={"role", "phone", "departure_date", "user_id"},
     *              @OA\Property(
     *                  property="role",
     *                  type="string",
     *                  description="O papel do membro."
     *              ),
     *              @OA\Property(
     *                  property="phone",
     *                  type="string",
     *                  description="O número de telefone do membro (deve ter pelo menos 11 caracteres)."
     *              ),
     *              @OA\Property(
     *                  property="departure_date",
     *                  type="date",
     *                  description="A data de partida do membro (formato YYYY-MM-DD)."
     *              ),
     *              @OA\Property(
     *                  property="user_id",
     *                  type="string",
     *                  description="O ID do usuário associado ao membro."
     *              )
     *          )
     *      )
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
    public function store(MemberRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, Member::class);

        $payload = $request->validated();
        $typeGroup = $this->memberRepository->create($payload);
        return response()->json($typeGroup, 201);
    }

    /**
     * @OA\Get(
     *   path="/members/{id}",
     *   tags={"members"},
     *   summary="Lista o registro de membro por ID",
     *   description="Lista o registro de membro por ID de referência,
     *   somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do membro",
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
        $this->authorize(AbilitiesEnum::VIEW, Member::class);

        $user = $this->memberRepository->findWithUser($id);
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *   path="/members/{id}",
     *   tags={"members"},
     *   summary="Atualizar membro",
     *   description="Atualizar membro: Apenas o membro pode atualizar suas próprias informações,
     *   somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do membro",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="role",
     *                  type="string",
     *                  description="O papel do membro."
     *              ),
     *              @OA\Property(
     *                  property="phone",
     *                  type="string",
     *                  description="O número de telefone do membro (deve ter pelo menos 11 caracteres)."
     *              ),
     *              @OA\Property(
     *                  property="departure_date",
     *                  type="date",
     *                  description="A data de partida do membro (formato YYYY-MM-DD)."
     *              ),
     *              @OA\Property(
     *                  property="user_id",
     *                  type="string",
     *                  description="O ID do usuário associado ao membro."
     *              )
     *          )
     *      )
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

    public function update(string $id, MemberRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, Member::class);
        $payload = $request->validated();
        $user = $this->memberRepository->update($id, $payload);
        return response()->json($user, 200);
    }

    /**
     * @OA\Delete(
     *   path="/members/{id}",
     *   tags={"members"},
     *   summary="Deletar membro",
     *   description="Deletar membro por ID de referência, somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do membro",
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
    public function destroy(string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::DELETE, Member::class);
        $this->memberRepository->delete($id);
        return response()->json([], 204);
    }
}
