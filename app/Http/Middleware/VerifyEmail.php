<?php

namespace App\Http\Middleware;

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
        if (Auth::user()->email_verified_at === null) {
            return response(
                [
                    'success' => false,
                    'message' => 'Please verify your email before you can continue'
                ],
                401
            );
        }
        return $next($request);
    }
}
