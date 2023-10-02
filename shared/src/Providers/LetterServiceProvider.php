<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Latte\Engine;
use Latte\Sandbox\SecurityPolicy;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswer;
use MinVWS\DUSi\Shared\Application\DTO\LetterData;
use MinVWS\DUSi\Shared\Application\DTO\LetterStageData;
use MinVWS\DUSi\Shared\Application\DTO\LetterStages;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Services\LatteLetterLoaderService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LetterServiceProvider extends ServiceProvider
{
    private const VIEWS_BASE_PATH = __DIR__ . '/../../resources/views';

    public function register()
    {
        $this->app->singleton(Engine::class, function ($app) {
            $latteLoaderService = new LatteLetterLoaderService(self::VIEWS_BASE_PATH . '/letters');

            $latte = new Engine();
            $latte->setSandboxMode();
            $latte->setTempDirectory($app->config->get('view.compiled'));
            $latte->setLoader($latteLoaderService);

            $policy = new SecurityPolicy();
            $policy->allowTags(['block', 'if', 'else', 'elseif', '=', 'layout', 'include']);
            $policy->allowFilters([
                'dataStream', 'date', 'join', 'spaceless', 'capitalize',
                'firstUpper', 'lower', 'upper', 'round'
            ]);

            $policy->allowProperties(LetterData::class, $policy::All);
            $policy->allowProperties(LetterStages::class, $policy::All);
            $policy->allowProperties(LetterStageData::class, $policy::All);
            $policy->allowProperties(ApplicationStageAnswer::class, $policy::All);
            $policy->allowMethods(CarbonImmutable::class, $policy::All);

            $latte->setPolicy($policy);

            return $latte;
        });
    }

    public function boot(): void
    {
        $this->app->when(ApplicationFileRepository::class)
            ->needs(Filesystem::class)
            ->give(function (Application $app) {
                return $app->make(FilesystemManager::class)->disk(Disk::APPLICATION_FILES);
            });

        $this->loadViewsFrom(self::VIEWS_BASE_PATH, 'dusi');
    }
}
