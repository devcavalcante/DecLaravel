<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
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
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            if (!$request->wantsJson()) {
                throw $e;
            }

            if ($response = $this->auth->onceBasic()) {
                return $response;
            }
        }

        return $next($request);
    }

    protected function redirectTo(Request $request)
    {
        throw new UnauthorizedException();
    }
}
