<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Exceptions\MembersExists;
use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\MemberService;
use App\Transformer\MemberTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Throwable;

/**
 * @OA\Tag(
 *     name="Members",
 *     description="CRUD dos membros, apenas usuários do tipo REPRESENTANTES podem criar, atualizar e editar membros"
 * )
 */
class MemberController extends Controller
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
        private readonly MemberService $memberService
    ) {
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
     */
    public function index(): JsonResponse
    {
        $members = $this->memberRepository->listAll();
        return response()->json($this->transform(new MemberTransformer(), $members));
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
     * @throws MembersExists|Throwable
     */
    public function store(MemberRequest $request, string $groupId): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Member::class, $groupId]);
        $this->memberService->createMany($groupId, $request->all());
        return response()->json([], 201);
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
        $user = $this->memberRepository->findById($id);
        return response()->json($this->transform(new MemberTransformer(), $user));
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
        $this->authorize(AbilitiesEnum::UPDATE, [Member::class, $id]);
        $payload = $request->all();
        $user = $this->memberService->edit($id, $payload);
        return response()->json($user);
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
     * @throws Throwable
     */
    public function destroy(string $groupId, string $id): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Member::class, $groupId]);
        $this->memberRepository->delete($id);
        return response()->json([], 204);
    }
}
