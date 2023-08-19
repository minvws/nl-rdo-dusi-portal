<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\OidcLoginResponseHandler;
use Illuminate\Support\ServiceProvider;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;

class OidcServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginResponseHandlerInterface::class, OidcLoginResponseHandler::class);
    }
}
