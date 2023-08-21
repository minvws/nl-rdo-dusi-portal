<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers\Auth;

use MinVWS\DUSi\Application\API\Http\Controllers\Controller;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class DigidMockController extends Controller
{
    private LoginResponseHandlerInterface $loginResponseHandler;

    public function __construct(LoginResponseHandlerInterface $loginResponseHandler)
    {
        $this->loginResponseHandler = $loginResponseHandler;
    }

    public function login(): Response
    {
        return $this->loginResponseHandler->handleLoginResponse(
            (object)[
            "bsn" => "942424242",
            ]
        );
    }
}
