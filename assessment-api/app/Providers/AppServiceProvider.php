<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (Config::get('fortify.disable_2fa')) {
            Fortify::ignoreRoutes();
            $features = config('fortify.features');
            $updatedFeatures = array_diff($features, ['two-factor-authentication']);
            config(['fortify.features' => $updatedFeatures]);
        }
    }
}
