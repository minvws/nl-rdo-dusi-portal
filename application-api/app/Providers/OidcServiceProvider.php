<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Providers;

use MinVWS\DUSi\Application\API\Http\Responses\OidcLoginResponseHandler;
use Illuminate\Support\ServiceProvider;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;

class OidcServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginResponseHandlerInterface::class, OidcLoginResponseHandler::class);
    }
}
