<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

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

        return response(['errors' => 'Unauthorized.', 'code' => 401], 401);
    }
}
