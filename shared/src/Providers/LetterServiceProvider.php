<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeImmutable;
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
use MinVWS\DUSi\Shared\Subsidy\Models\Disk as SubsidyDisk;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyFileRepository;

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
            $latte->addFunction('formatCurrency', fn($amount) =>
                number_format((float)$amount, 2, ',', '.'));

            $policy = new SecurityPolicy();
            $policy->allowTags(['block', 'if', 'else', 'elseif', '=', 'layout', 'include', 'var']);
            $policy->allowFilters([
                'dataStream', 'date', 'join', 'spaceless', 'capitalize',
                'firstUpper', 'lower', 'upper', 'round', 'breakLines', 'nocheck'
            ]);

            $policy->allowProperties(LetterData::class, $policy::All);
            $policy->allowProperties(LetterStages::class, $policy::All);
            $policy->allowProperties(LetterStageData::class, $policy::All);
            $policy->allowProperties(ApplicationStageAnswer::class, $policy::All);
            $policy->allowMethods(DateTime::class, $policy::All);
            $policy->allowMethods(DateTimeImmutable::class, $policy::All);
            $policy->allowMethods(Carbon::class, $policy::All);
            $policy->allowMethods(CarbonImmutable::class, $policy::All);
            $policy->allowMethods(LetterData::class, ['getSignature']);
            $policy->allowFunctions(['formatCurrency']);

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

        $this->app->when(SubsidyFileRepository::class)
            ->needs(Filesystem::class)
            ->give(function (Application $app) {
                return $app->make(FilesystemManager::class)->disk(SubsidyDisk::SUBSIDY_FILES);
            });

        $this->loadViewsFrom(self::VIEWS_BASE_PATH, 'dusi');
    }
}
