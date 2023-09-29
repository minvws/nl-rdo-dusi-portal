<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\UpdatesUserPasswords as UpdatesUserPasswordContract;
use Laravel\Fortify\Fortify;
use MinVWS\DUSi\Assessment\API\Fortify\Actions\UpdateUserPassword;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            UpdatesUserPasswordContract::class,
            UpdateUserPassword::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
