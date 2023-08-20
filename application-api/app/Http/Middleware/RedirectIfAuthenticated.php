<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Middleware;

use MinVWS\DUSi\Application\API\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (RedirectResponse) $next
     * @param  string|null ...$guards
     * @return RedirectResponse|Redirector
     */
    public function handle(Request $request, Closure $next, ...$guards): RedirectResponse|Redirector
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
