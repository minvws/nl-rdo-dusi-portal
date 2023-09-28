<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Console\Commands\Hsm\HsmInfoCommand;
use MinVWS\DUSi\Shared\Application\Console\Commands\Hsm\HsmLocalClearCommand;
use MinVWS\DUSi\Shared\Application\Console\Commands\Hsm\HsmLocalInitCommand;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmService;
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HsmApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/hsm_api.php' => config_path('hsm_api.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([HsmInfoCommand::class]);
            $this->commands([HsmLocalClearCommand::class]);
            $this->commands([HsmLocalInitCommand::class]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/hsm_api.php',
            'hsm_api'
        );

        $this->app->singleton(HsmService::class, function (Application $app) {
            $config = $app->make('config');

            if (empty($config->get('hsm_api.endpoint_url'))) {
                throw new RuntimeException('HSM API endpoint URL must be set in the environment config.
                Please set HSM_API_ENDPOINT_URL.');
            }
            if (empty($config->get('hsm_api.client_certificate_path'))) {
                throw new RuntimeException('HSM API Client certificate path must be set in the environment
                config. Please set HSM_API_CLIENT_CERTIFICATE_PATH.');
            }
            if (empty($config->get('hsm_api.client_certificate_key_path'))) {
                throw new RuntimeException('HSM API Client certificate key path must be set in the environment
                 config. Please set HSM_API_CLIENT_CERTIFICATE_KEY_PATH.');
            }
            if (empty($config->get('hsm_api.module'))) {
                throw new RuntimeException('HSM API module must be set in the environment config. Please set
                 HSM_API_MODULE.');
            }
            if (empty($config->get('hsm_api.slot'))) {
                throw new RuntimeException('HSM API slot must be set in the environment config. Please set
                 HSM_API_SLOT.');
            }

            return new HsmService(
                client: new Client([
                    'base_uri' => $config->get('hsm_api.endpoint_url'),
                    'verify' => false,
                    'cert' => $config->get('hsm_api.client_certificate_path'),
                    'ssl_key' => $config->get('hsm_api.client_certificate_key_path')
                ]),
                endpointUrl: $config->get('hsm_api.endpoint_url'),
                module: $config->get('hsm_api.module'),
                slot: $config->get('hsm_api.slot'),
            );
        });

        $this->app->singleton(HsmInfoCommand::class, function (Application $app) {
            $config = $app->make('config');

            return new HsmInfoCommand(
                service: $app->make(HsmService::class),
                hsmApiModule: $config->get('hsm_api.module') ?? '',
                hsmApiSlot: $config->get('hsm_api.slot') ?? '',
                hsmEncryptionKeyLabel: $config->get('hsm_encryption.key_label') ?? '',
            );
        });

        $this->app->singleton(HsmLocalClearCommand::class, function (Application $app) {
            $config = $app->make('config');

            return new HsmLocalClearCommand(
                environment: $config->get('app.env'),
                debugModeEnabled: $config->get('app.debug'),
                service: $app->make(HsmService::class),
                hsmApiModule: $config->get('hsm_api.module') ?? '',
                hsmApiSlot: $config->get('hsm_api.slot') ?? '',
                hsmApiEncryptionKeyLabel: $config->get('hsm_encryption.key_label') ?? '',
            );
        });

        $this->app->singleton(HsmLocalInitCommand::class, function (Application $app) {
            $config = $app->make('config');

            return new HsmLocalInitCommand(
                service: $app->make(HsmService::class),
                hsmApiModule: $config->get('hsm_api.module') ?? '',
                hsmApiSlot: $config->get('hsm_api.slot') ?? '',
                hsmApiEncryptionKeyLabel: $config->get('hsm_encryption.key_label') ?? '',
            );
        });
    }
}
