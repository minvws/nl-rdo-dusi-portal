<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ApplicationService;
use App\Services\ApplicationSubsidyService;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
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
        $this->app->singleton(ApplicationService::class, function ($app) {
            return new ApplicationService($app->make(ApplicationRepository::class));
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
