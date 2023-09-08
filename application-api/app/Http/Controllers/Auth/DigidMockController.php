<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers\Auth;

use MinVWS\DUSi\Application\API\Http\Controllers\Controller;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class DigidMockController extends Controller
{
    public function __construct(
        private readonly LoginResponseHandlerInterface $loginResponseHandler,
        private readonly OidcUserLoa $mockLoa = null
    ) {
    }

    public function login(): Response
    {
        return $this->loginResponseHandler->handleLoginResponse(
            (object)[
                "bsn" => "942424242",
                "loa_authn" => $this->mockLoa->value
            ]
        );
    }
}
