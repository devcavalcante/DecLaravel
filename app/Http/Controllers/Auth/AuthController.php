<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @OA\Tag(
 *     name="auth",
 *     description="Autenticação dos usuários"
 * )
 * @OA\PathItem(path="/auth")
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * @OA\Post(
     *   path="/register",
     *   tags={"auth"},
     *   summary="Criar novo usuário",
     *   description="Criação de novo usuário",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                  "name": "Nome do usuário",
     *                  "email": "Email do usuário",
     *                  "password": "Senha do usuário",
     *                  "c_password": "Confirmação de senha",
     *              }
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response="422",
     *     description="Erro de validação"
     *   ),
     * )
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function register(UserRequest $userRequest): JsonResponse
    {
        $data = $userRequest->all();
        $user = $this->authService->register($data);
        return response()->json($user, 201);
    }

    /**
     * @OA\Post(
     *   path="/login",
     *   tags={"auth"},
     *   summary="Login do usuário",
     *   description="Usuário loga no sistema",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                  "email": "Email do usuário",
     *                  "password": "Senha do usuário",
     *              }
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response="422",
     *     description="Erro de validação"
     *   ),
     *   @OA\Response(
     *     response="401",
     *     description="Unauthorized"
     *   )
     * )
     * @throws Throwable
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->all();
        $user = $this->authService->login($data);
        return response()->json($user);
    }

    /**
     * @OA\Post(
     *   path="/users/logout",
     *   tags={"auth"},
     *   summary="Desloga usuário",
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
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json([], 204);
    }

    /**
     * @OA\Post(
     *   path="/forgot-password",
     *   tags={"auth"},
     *   summary="Envia e-mail para o usuário recuperar a senha",
     *   description="Envio de email para resetar a senha do usuário",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                  "email": "Email do usuário"
     *              }
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response="422",
     *     description="Erro de validação"
     *   )
     * )
     * @throws Throwable
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->only('email');
        $response = $this->authService->forgotPassword($email);
        return response()->json($response);
    }

    /**
     * @OA\Post(
     *   path="/reset-password",
     *   tags={"auth"},
     *   summary="Editar senha",
     *   description="Reseta a senha do usuário",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              example={
     *                  "email": "Email do usuário",
     *                  "token": "Token enviado por e-mail",
     *                  "password": "Nova senha",
     *                  "password_confirmation": "Confirmar a nova senha"
     *              }
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK"
     *   ),
     *   @OA\Response(
     *     response="500",
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response="422",
     *     description="Erro de validação"
     *   )
     * )
     * @throws Throwable
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->only('email', 'password', 'password_confirmation', 'token');
        $response = $this->authService->resetPassword($data);
        return response()->json($response, 201);
    }
}
