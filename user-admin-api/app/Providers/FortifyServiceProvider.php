<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Providers;

use Illuminate\Support\Facades\Hash;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\User\Admin\API\Actions\Fortify\UpdateUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            if (
                $user &&
                $user->active &&
                $user->hasRole([
                    Role::UserAdmin,
                ]) &&
                Hash::check($request->password, $user->password)
            ) {
                return $user;
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::viewPrefix('auth.');

        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
