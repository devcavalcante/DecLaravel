<?php

namespace App\Http\Middleware;

use App\Repositories\Interfaces\ApiTokenRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
    public function __construct(protected ApiTokenRepositoryInterface $apiTokenRepository)
    {
    }
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Token');
        dd($token);
        $apiToken = $this->apiTokenRepository->findByFilters(['api_token' => $token]);

        if (!$token || $apiToken->isEmpty()) {
            return response()->json(['error' => 'Não autorizado.', 'code' => 403], 403);
        }

        if ($apiToken->first() && $apiToken->first()->api_token_expires_at
            && now()->gt($apiToken->first()->api_token_expires_at)
        ) {
            return response()->json(['error' => 'Token expirado.', 'code' => 401], 401);
        }

        return $next($request);
    }
}
