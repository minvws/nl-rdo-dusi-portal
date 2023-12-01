<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Console\Commands\CheckSurePay;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\BankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\MockedBankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\SurePayRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
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


        if (config('surepay_api.enabled')) {
            $this->app->singleton(SurePayClient::class, fn() => $this->buildSurePayClient());
            $this->app->bind(BankAccountRepository::class, SurePayRepository::class);
        } else {
            $this->app->bind(BankAccountRepository::class, MockedBankAccountRepository::class);
        }
    }

    private function buildSurePayClient(): ?SurePayClient
    {
        if (!config('surepay_api.enabled')) {
            return null;
        }

        if (empty(config('surepay_api.endpoint'))) {
            throw new RuntimeException(
                'Please set the env SUREPAY_ENDPOINT to the SurePay API endpoint URL.'
            );
        }

        $options = [
            'base_uri' => config('surepay_api.endpoint'),
            'verify' => config('surepay_api.verify_ssl', true),
            'proxy' => []
        ];

        if (!empty(config('surepay_api.proxy.http'))) {
            $options['proxy']['http'] = config('surepay_api.proxy.http');
        }

        if (!empty(config('surepay_api.proxy.https'))) {
            $options['proxy']['https'] = config('surepay_api.proxy.https');
        }

        return new SurePayClient(client: new Client($options));
    }
}
