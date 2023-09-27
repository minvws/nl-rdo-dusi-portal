<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifyCsrfToken;

class VerifyCsrfToken extends BaseVerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/api/login',
        '/api/logout',
        '/api/two-factor-challenge'
    ];

    /**
     * We use the XSRF-TOKEN cookie as our default CSRF token response.
     *
     * @param $request
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
