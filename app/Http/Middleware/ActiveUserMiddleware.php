<?php

namespace App\Http\Middleware;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;

use Closure;
use Illuminate\Http\Request;

class ActiveUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user() && auth()->user()->is_active == 1) {
            return $next($request);
        }

        return ApiResponse::errorResponse('Unauthorized!, Your account has been deactivated, please contact an administrator', Response::HTTP_UNAUTHORIZED);
    }
}
