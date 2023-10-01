<?php

namespace App\Http\Middleware;

use App\Helpers\Env;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::user()->email_verified_at === null || !$this->checkEnvIsEnvironmentDevelopment()) {
            return response(
                [
                    'success' => false,
                    'message' => 'Verifique seu email antes de continuar',
                ],
                401
            );
        }
        return $next($request);
    }

    private function checkEnvIsEnvironmentDevelopment(): bool
    {
        if (env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing') {
            return true;
        }
        return false;
    }
}
