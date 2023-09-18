<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use MinVWS\DUSi\Application\API\Interfaces\KeyReader;
use MinVWS\DUSi\Application\API\Repositories\CacheRepository;
use Illuminate\Cache\CacheManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\API\Services\FileKeyReader;
use MinVWS\DUSi\Application\API\Services\HsmEncryptionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->when(FileKeyReader::class)
            ->needs('$publicKeyPath')
            ->giveConfig('hsm_encryption.public_key_path');

        $this->app->singleton(KeyReader::class, FileKeyReader::class);

        $this->app->singleton(
            CacheRepository::class,
            function (Application $app) {
                return new CacheRepository($app->get(CacheManager::class)->store('form'), config('form.cache_ttl'));
            }
        );

        $this->app->when(HsmEncryptionService::class)
            ->needs('$hsmEncryptionKeyLabel')
            ->giveConfig('hsm_encryption.key_label');
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
