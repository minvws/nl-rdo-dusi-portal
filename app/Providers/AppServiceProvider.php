<?php

namespace App\Providers;

use App\Repositories\FormCacheRepository;
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
    public function register()
    {
        $this->app->singleton(FormCacheRepository::class, function (Application $app) {
            return new FormCacheRepository($app->get(CacheManager::class)->store('form'), config('form.cache_ttl'));
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
