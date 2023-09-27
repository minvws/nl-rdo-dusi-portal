<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Services\SurePay\SurePayService;
use RuntimeException;

class SurePayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SurePayService::class, function () {
            $config = config('surepay_api');

            if (empty($config->get('endpoint'))) {
                throw new RuntimeException(
                    'Please set the env SUREPAY_ENDPOINT to the SurePay API endpoint URL.'
                );
            }

            return new SurePayService(
                client: new Client([
                    'base_uri' => $config->get('endpoint'),
                    'verify'   => false,
                ]),
            );
        });
    }
}
