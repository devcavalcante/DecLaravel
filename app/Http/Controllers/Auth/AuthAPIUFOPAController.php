<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthorizedException;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthAPIService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use OpenApi\Annotations as OA;
use Throwable;

class AuthAPIUFOPAController extends Controller
{
    public function __construct(private AuthAPIService $authService)
    {
    }

    /**
     * @OA\GET(
     *   path="/redirect",
     *   tags={"auth"},
     *   summary="Redireciona o usuário para o login da ufopa",
     *   description="Endpoint para redirecionar para o login da ufopa",
     *   @OA\Response(
     *    response="default", description="Redireciona para a URL de autorização"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     * )
     */
    public function redirect(): RedirectResponse
    {
        $authorizationUrl = $this->authService->getAuthorizationUrl();
        return Redirect::away($authorizationUrl);
    }

    /**
     * @OA\Get(
     *     path="/callback",
     *     tags={"auth"},
     *     summary="Retorno para salvar usuário",
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         required=true,
     *         description="Código de autorização recebido no retorno",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(type="object", description="Informação do usuário")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(type="object", description="Error")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(type="object", description="Error response")
     *     ),
     * )
     *
     * @throws GuzzleException
     * @throws Throwable
     */
    public function handleCallback(): JsonResponse
    {
        $code = request()->query('code');
        $user = $this->authService->loginWithAPIUFOPA($code);
        return response()->json($user);
    }

    /**
     * @OA\Post(
     *   path="/users/logout-ufopa",
     *   tags={"auth"},
     *   summary="Desloga usuário logado pela api da ufopa",
     *   description="Endpoint para revogar o token",
     *   @OA\Response(
     *     response=204,
     *     description="Not Content"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     * )
     * @throws AuthorizedException
     */
    public function logoutUser(): JsonResponse
    {
        $token = request()->header('token');
        $this->authService->logoutUsers($token);
        return response()->json([], 204);
    }
}
