<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifyCsrfToken;

class VerifyCsrfToken extends BaseVerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * We use the XSRF-TOKEN cookie as our default CSRF token response.
     *
     * @return string
     */
    protected function getTokenFromRequest($request): string
    {
        $token = parent::getTokenFromRequest($request);
        if (empty($token)) {
            $token = $request->cookies->get('XSRF-TOKEN', '');
            if (!is_string($token)) {
                $token = '';
            }
        }

        return $token;
    }
}
