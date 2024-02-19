<?php

namespace App\Http\Controllers;

use App\Enums\AbilitiesEnum;
use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\MemberService;
use App\Transformer\MemberTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * @OA\Tag(
 *     name="members",
 *     description="CRUD dos membros, apenas usuários do tipo REPRESENTANTES podem criar, atualizar e editar membros"
 * )
 */
class MemberController extends Controller
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
        private readonly MemberService $memberService,
        private readonly GroupRepositoryInterface $groupRepository,
    ) {
    }

    /**
     * @OA\Get(
     *   path="/groups/{groupId}/members",
     *   tags={"members"},
     *   summary="Listar todos os membros",
     *   description="Lista todos os membros: ADMINISTRADOR, REPRESENTANTE E GERENTE têm acesso a este endpoint.",
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
        $group = $this->groupRepository->findById($groupId);
        $members = $group->members;

        return response()->json($this->transform(new MemberTransformer(), $members));
    }

    /**
     * @OA\Post(
     *   path="/groups/{groupId}/members",
     *   tags={"members"},
     *   summary="Criar novo membro",
     *   description="Cria um novo membro, somente o REPRESENTANTE tem acesso a este endpoint.",
     *  @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="Id do grupo que o membro sera associado",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                          {
     *                              "name": "Debora",
     *                              "email": "bar@mail.com",
     *                              "role": "bar",
     *                              "phone": "93991185489",
     *                              "entry_date": "23-10-1998",
     *                              "departure_date": "23-10-2023"
     *                          },
     *                          {
     *                              "name": "Emily",
     *                              "email": "outromail@mail.com",
     *                              "role": "bar",
     *                              "phone": "93991185489",
     *                              "entry_date": "23-10-1998",
     *                              "departure_date": "23-10-2023"
     *                          }
     *                     }
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
     * @throws Throwable
     */
    public function store(MemberRequest $request, string $groupId): JsonResponse
    {
        $this->authorize(AbilitiesEnum::CREATE, [Member::class, $groupId]);
        $this->memberService->createMany($groupId, $request->all());
        return response()->json([], 201);
    }

    /**
     * @OA\Get(
     *   path="/groups/{groupId}/members/{id}",
     *   tags={"members"},
     *   summary="Lista o registro de membro por ID",
     *   description="Lista o registro de membro por ID de referência.",
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
    public function show(string $groupId, string $id): JsonResponse
    {
        $user = $this->memberRepository->findById($id);
        return response()->json($this->transform(new MemberTransformer(), $user));
    }

    /**
     * @OA\Put(
     *   path="/groups/{groupId}/members/{id}",
     *   tags={"members"},
     *   summary="Atualizar membro",
     *   description="somente o REPRESENTANTE tem acesso a este endpoint.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id do membro",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
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
     *                  property="email",
     *                  type="string",
     *                  description="Email do membro."
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
    public function update(string $groupId, string $id, MemberRequest $request): JsonResponse
    {
        $this->authorize(AbilitiesEnum::UPDATE, [Member::class, $groupId]);
        $payload = $request->all();
        $member = $this->memberService->edit($id, $payload);
        return response()->json($member);
    }

    /**
     * @OA\Delete(
     *   path="/groups/{groupId}/members/{memberID}",
     *   tags={"members"},
     *   summary="Deletar membro",
     *   description="Deletar membro por ID de referência, somente o REPRESENTANTE tem acesso a este endpoint.",
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
        $this->authorize(AbilitiesEnum::DELETE, [Member::class, $groupId]);
        $this->memberService->delete($groupId, $id);
        return response()->json([], 204);
    }
}
