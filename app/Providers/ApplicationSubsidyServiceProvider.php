<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ApplicationSubsidyService;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class ApplicationSubsidyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApplicationSubsidyService::class, function ($app) {
            return new ApplicationSubsidyService($app->make(SubsidyRepository::class));
        });
    }
}
