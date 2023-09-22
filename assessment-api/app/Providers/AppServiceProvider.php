<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Laravel\Fortify\Fortify;
use MinVWS\DUSi\Assessment\API\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Assessment\API\Services\LatteLetterLoaderService;
use MinVWS\DUSi\Assessment\API\DTO\LetterData;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStages;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStageData;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStageAnswer;
use Latte\Engine;
use Latte\Sandbox\SecurityPolicy;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Models\Disk;

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
        $this->app->singleton(Engine::class, function ($app) {
            $latte = new Engine();
            $latte->setSandboxMode();
            $latte->setTempDirectory($app->config->get('view.compiled'));
            $latte->setLoader(new LatteLetterLoaderService(resource_path('views/letters')));

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
    public function boot(): void
    {
        $this->app->when(ApplicationFileRepository::class)
            ->needs(Filesystem::class)
            ->give(function (Application $app) {
                return $app->make(FilesystemManager::class)->disk(Disk::APPLICATION_FILES);
            });

        if (Config::get('fortify.disable_2fa')) {
            Fortify::ignoreRoutes();
            $features = config('fortify.features');
            $updatedFeatures = array_diff($features, ['two-factor-authentication']);
            config(['fortify.features' => $updatedFeatures]);
        }
    }
}
