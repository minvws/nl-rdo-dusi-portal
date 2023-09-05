<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use Symfony\Component\HttpFoundation\Response;

class RequireClientPublicKey
{
    public function __construct(private ClientPublicKeyHelper $helper)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->helper->requireClientPublicKey();
        return $next($request);
    }
}
