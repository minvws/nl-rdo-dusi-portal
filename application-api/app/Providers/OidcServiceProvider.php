<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Providers;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use MinVWS\DUSi\Application\API\Exceptions\OidcExceptionHandler;
use MinVWS\DUSi\Application\API\Http\Responses\OidcLoginResponseHandler;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use MinVWS\OpenIDConnectLaravel\Services\ExceptionHandlerInterface;

class OidcServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseHandlerInterface::class, function (Application $app) {
            $config = $app->make(ConfigRepository::class);

            $frontendBaseUrl = $config->get('app.frontend_base_url');
            $loaString = $config->get('oidc.minimum_loa');

            $loa = null;
            if (!empty($loaString)) {
                $loa = OidcUserLoa::from($loaString);
            }

            return new OidcLoginResponseHandler(
                frontendBaseUrl: $frontendBaseUrl,
                minimumLoa: $loa,
            );
        });
        $this->app->bind(ExceptionHandlerInterface::class, OidcExceptionHandler::class);
    }
}
