<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Providers;

use GuzzleHttp\Client;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\Backend\Handlers\FileUploadHandler;
use MinVWS\DUSi\Application\Backend\Handlers\FormSubmitHandler;
use MinVWS\DUSi\Application\Backend\Interfaces\KeyReader;
use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Application\Backend\Services\FileKeyReader;
use MinVWS\DUSi\Application\Backend\Services\Hsm\HsmService;
use MinVWS\DUSi\Application\Backend\Services\IdentityService;
use MinVWS\DUSi\Application\Backend\Services\SurePay\SurePayService;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FileUploadHandlerInterface;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FormSubmitHandlerInterface;
use MinVWS\DUSi\Application\Backend\Console\Commands\Hsm\HsmInfoCommand;
use MinVWS\DUSi\Application\Backend\Console\Commands\Hsm\HsmLocalClearCommand;
use MinVWS\DUSi\Application\Backend\Console\Commands\Hsm\HsmLocalInitCommand;
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(KeyReader::class, FileKeyReader::class);

        $this->app->bind(
            FileUploadHandlerInterface::class,
            function (Application $app) {
                return new FileUploadHandler($app->get(ApplicationService::class));
            }
        );
        $this->app->bind(
            FormSubmitHandlerInterface::class,
            function (Application $app) {
                return new FormSubmitHandler($app->get(ApplicationService::class));
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->app->singleton(SurePayService::class, function () {
            $config = config('surepay_api');

            if (empty($config->get('endpoint'))) {
                throw new RuntimeException('SurePay API endpoint URL must be set in the environment config.
                Please set SUREPAY_ENDPOINT.');
            }

            return new SurePayService(
                client: new Client([
                    'base_uri' => $config->get('endpoint'),
                    'verify' => false,
                ]),
            );
        });

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
                hsmApiEncryptionKeyLabel: $config->get('hsm_api.encryption_key_label') ?? '',
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
                hsmApiEncryptionKeyLabel: $config->get('hsm_api.encryption_key_label') ?? '',
            );
        });

        $this->app->singleton(HsmLocalInitCommand::class, function (Application $app) {
            $config = $app->make('config');

            return new HsmLocalInitCommand(
                service: $app->make(HsmService::class),
                hsmApiModule: $config->get('hsm_api.module') ?? '',
                hsmApiSlot: $config->get('hsm_api.slot') ?? '',
                hsmApiEncryptionKeyLabel: $config->get('hsm_api.encryption_key_label') ?? '',
            );
        });

        $this->app->when(IdentityService::class)->needs('$hashSecret')->giveConfig('identity.hash_secret');
        $this->app->when(IdentityService::class)->needs('$hashAlgorithm')->giveConfig('identity.hash_algorithm');
    }
}
