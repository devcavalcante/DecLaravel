<?php

namespace App\Http\Middleware;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
    public function __construct(protected UserRepositoryInterface $userRepository)
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
        $token = Str::substr($request->header('Token'), 7);
        $user = $this->userRepository->findByFilters(['api_token' => $token]);

        if (!$token || $user->isEmpty()) {
            return response()->json(['error' => 'Não autorizado.', 'code' => 401], 401);
        }

        if ($user->first() && $user->first()->api_token_expires_at && now()->gt($user->first()->api_token_expires_at)) {
            return response()->json(['error' => 'Token expirado.', 'code' => 401], 401);
        }

        return $next($request);
    }
}
