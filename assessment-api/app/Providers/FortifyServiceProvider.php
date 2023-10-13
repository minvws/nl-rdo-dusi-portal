<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\UpdatesUserPasswords as UpdatesUserPasswordContract;
use Laravel\Fortify\Fortify;
use MinVWS\DUSi\Assessment\API\Fortify\Actions\UpdateUserPassword;
use MinVWS\DUSi\Assessment\API\Fortify\PasswordBrokerManager;
use MinVWS\DUSi\Assessment\API\Models\Scopes\UserRoleScope;
use MinVWS\DUSi\Assessment\API\Services\FrontendRouteService;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Scope all users to prevent login and requesting password reset links for not allowed users
        // Password resets for inactive users are still possible but could not login
        User::addGlobalScope(new UserRoleScope([
            Role::Assessor,
            Role::ImplementationCoordinator,
            Role::InternalAuditor,
        ]));

        $this->app->singleton(
            UpdatesUserPasswordContract::class,
            UpdateUserPassword::class
        );

        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            if (
                $user &&
                $user->active &&
                Hash::check($request->password, $user->password)
            ) {
                return $user;
            }
        });

        ResetPasswordNotification::createUrlUsing(function (CanResetPassword $notifiable, string $token): string {
            $service = $this->app->make(FrontendRouteService::class);

            return $service->route('password-reset', [
                'email' => $notifiable->getEmailForPasswordReset(),
                'token' => $token,
            ]);
        });
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
