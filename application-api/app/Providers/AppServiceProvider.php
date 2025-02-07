<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use MinVWS\DUSi\Application\API\Repositories\CacheRepository;
use Illuminate\Cache\CacheManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(
            CacheRepository::class,
            function (Application $app) {
                return new CacheRepository($app->get(CacheManager::class)->store('form'), config('form.cache_ttl'));
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
        //
    }
}
