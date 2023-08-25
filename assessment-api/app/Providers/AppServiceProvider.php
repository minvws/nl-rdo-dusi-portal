<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

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
            return new ApplicationSubsidyService($app->make(SubsidyRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
