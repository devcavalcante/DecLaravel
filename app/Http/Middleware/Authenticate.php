<?php

namespace App\Http\Middleware;

use App\Repositories\Interfaces\ApiTokenRepositoryInterface;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        if ($this->auth->guard('api')->check()) {
            return $next($request);
        }

        $token = $request->header('Authorization');
        if (!$token || !$this->checkTokenApi($token)) {
            throw new UnauthorizedException('Não autorizado', 401);
        }

        if ($this->checkTokenApi($token)) {
            return $next($request);
        }
        throw new UnauthorizedException('Não autorizado', 401);
    }

    private function checkTokenApi(string $token): bool
    {
        $apiTokenRepository = app(ApiTokenRepositoryInterface::class);
        if ($token) {
            $apiToken = $apiTokenRepository->findByFilters(['api_token' => $token])->first();

            if (!$apiToken) {
                throw new UnauthorizedException('Não autorizado', 401);
            }

            if ($apiToken->api_token_expires_at && now()->gt($apiToken->api_token_expires_at)) {
                throw new UnauthorizedException('Token Expirado', 401);
            }
            return true;
        }
        return false;
    }
}
