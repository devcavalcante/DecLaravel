<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthorizedException;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthAPIService;
use App\Transformer\UserTransformer;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use OpenApi\Annotations as OA;
use Throwable;

/**
 * @OA\Tag(
 *     name="auth",
 *     description="Autenticação dos usuários"
 * )
 * @OA\PathItem(path="/auth")
 */
class AuthAPIUFOPAController extends Controller
{
    public function __construct(private AuthAPIService $authService)
    {
    }

    public function redirect(): RedirectResponse
    {
        $authorizationUrl = $this->authService->getAuthorizationUrl();
        return Redirect::away($authorizationUrl);
    }

    /**
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
     * @throws AuthorizedException
     */
    public function logoutUser(): JsonResponse
    {
        $token = request()->header('token');
        $this->authService->logoutUsers($token);
        return response()->json([], 204);
    }
}
