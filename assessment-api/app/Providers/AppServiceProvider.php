<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Config;
use Laravel\Fortify\Fortify;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Assessment\API\Services\EncryptionService;
use MinVWS\DUSi\Assessment\API\Services\Hsm\HsmService;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ApplicationSubsidyService::class, function ($app) {
            return new ApplicationSubsidyService(
                $app->make(SubsidyRepository::class),
                $app->make(EncryptionService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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
        if (Config::get('fortify.disable_2fa')) {
            Fortify::ignoreRoutes();
            $features = config('fortify.features');
            $updatedFeatures = array_diff($features, ['two-factor-authentication']);
            config(['fortify.features' => $updatedFeatures]);
        }
    }
}
