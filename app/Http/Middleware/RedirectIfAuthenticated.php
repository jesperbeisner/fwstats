<?php

declare(strict_types=1);

namespace Fwstats\Http\Middleware;

use Closure;
use Fwstats\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class RedirectIfAuthenticated
{
    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = $guards === [] ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return to_route(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
