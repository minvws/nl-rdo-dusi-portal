<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MinVWS\DUSi\Shared\User\Models\User;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordUpdated
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!($user instanceof User)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        if ($user->passwordExpired(withinDays: 1)) {
            return response()->json([
                'message' => 'Unauthorized',
                'code' => 'password_expired',
            ], 401);
        }

        return $next($request);
    }
}
