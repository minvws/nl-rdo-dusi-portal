<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Console\Commands\CheckSurePay;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayRepository;
use RuntimeException;

class SurePayServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/surepay_api.php' => config_path('surepay_api.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([CheckSurePay::class]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/surepay_api.php',
            'surepay_api'
        );

        $this->app->singleton(SurePayRepository::class, function () {
            if (empty(config('surepay_api.endpoint'))) {
                throw new RuntimeException(
                    'Please set the env SUREPAY_ENDPOINT to the SurePay API endpoint URL.'
                );
            }

            return new SurePayRepository(
                client: new Client([
                    'base_uri' => config('surepay_api.endpoint'),
                    'verify' => false,
                ]),
            );
        });
    }
}
