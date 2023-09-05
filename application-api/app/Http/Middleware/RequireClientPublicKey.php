<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use Symfony\Component\HttpFoundation\Response;

class RequireClientPublicKey
{
    public const HEADER_NAME = 'X-DUS-I-Public-Key';

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->headers->has(self::HEADER_NAME)) {
            abort(400, sprintf('Missing %s header', self::HEADER_NAME));
        }

        $header = $request->headers->get(self::HEADER_NAME);
        assert(is_string($header));

        $publicKey = base64_decode($header, true);
        if ($publicKey === false) {
            abort(400, sprintf('Invalid %s header, make sure it is base64 encoded', self::HEADER_NAME));
        }

        // make available for injection
        app()->instance(ClientPublicKey::class, new ClientPublicKey($publicKey));

        return $next($request);
    }
}
