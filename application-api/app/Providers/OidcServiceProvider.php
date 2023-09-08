<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Providers;

use Config;
use MinVWS\DUSi\Application\API\Exceptions\OidcExceptionHandler;
use MinVWS\DUSi\Application\API\Http\Controllers\Auth\DigidMockController;
use MinVWS\DUSi\Application\API\Http\Responses\OidcLoginResponseHandler;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use MinVWS\OpenIDConnectLaravel\Services\ExceptionHandlerInterface;

class OidcServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app
            ->when(OidcLoginResponseHandler::class)
            ->needs('$frontendBaseUrl')
            ->giveConfig('app.frontend_base_url');
        $this->app
            ->when(OidcLoginResponseHandler::class)
            ->needs('$minimumLoa')
            ->give(fn () => OidcUserLoa::from(Config::get('oidc.minimum_loa')));

        $this->app->singleton(LoginResponseHandlerInterface::class, OidcLoginResponseHandler::class);
        $this->app->bind(ExceptionHandlerInterface::class, OidcExceptionHandler::class);

        $this->app
            ->when(DigidMockController::class)
            ->needs('mockLoa')
            ->give(fn () => OidcUserLoa::from(Config::get('oidc.minimum_loa')));
    }
}
