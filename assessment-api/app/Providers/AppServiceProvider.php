<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Config;
use Laravel\Fortify\Fortify;
use MinVWS\DUSi\Assessment\API\Services\LatteLetterLoaderService;
use MinVWS\DUSi\Assessment\API\DTO\LetterData;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStages;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStageData;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStageAnswer;
use Latte\Engine;
use Latte\Sandbox\SecurityPolicy;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Assessment\API\Services\EncryptionService;
use MinVWS\DUSi\Assessment\API\Services\Hsm\HsmService;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
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
    public function register()
    {
        $this->app->singleton(ApplicationSubsidyService::class, function ($app) {
            return new ApplicationSubsidyService(
                $app->make(SubsidyRepository::class),
                $app->make(EncryptionService::class)
            );
        });

        $this->app->singleton(Engine::class, function ($app) {
            $latte = new Engine();
            $latte->setSandboxMode();
            $latte->setTempDirectory($app->config->get('view.compiled'));
            $latte->setLoader(new LatteLetterLoaderService(resource_path('views') . '/letters/'));

            $policy = new SecurityPolicy();
            $policy->allowTags(['block', 'if', 'else', 'elseif', '=', 'layout', 'include']);
            $policy->allowFilters(['date', 'join', 'spaceless', 'capitalize', 'firstUpper', 'lower', 'upper', 'round']);

            $policy->allowProperties(LetterData::class, $policy::All);
            $policy->allowProperties(ApplicationStages::class, $policy::All);
            $policy->allowProperties(ApplicationStageData::class, $policy::All);
            $policy->allowProperties(ApplicationStageAnswer::class, $policy::All);

            $latte->setPolicy($policy);

            return $latte;
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
