<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\CompletePasswordReset as FortifyCompletePasswordReset;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPasswordResetLinkRequestResponseContract;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse
    as SuccessfulPasswordResetLinkRequestResponseContract;
use Laravel\Fortify\Contracts\UpdatesUserPasswords as UpdatesUserPasswordContract;
use Laravel\Fortify\Fortify;
use MinVWS\DUSi\Assessment\API\Fortify\Actions\CompletePasswordReset;
use MinVWS\DUSi\Assessment\API\Fortify\Actions\ResetUserPassword;
use MinVWS\DUSi\Assessment\API\Fortify\Actions\UpdateUserPassword;
use MinVWS\DUSi\Assessment\API\Fortify\PasswordBrokerManager;
use MinVWS\DUSi\Assessment\API\Fortify\Providers\AssessmentUserProvider;
use MinVWS\DUSi\Assessment\API\Fortify\Responses\SuccessfulPasswordResetLinkRequestResponse;
use MinVWS\DUSi\Assessment\API\Services\FrontendRouteService;
use MinVWS\DUSi\Shared\User\Enums\Role;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Scope all users to prevent login and requesting password reset links for not allowed users
        // Password resets for inactive users are still possible but could not login
        $allowedRoles = [
            Role::Assessor,
            Role::ImplementationCoordinator,
            Role::InternalAuditor,
            Role::DataExporter,
        ];

        Auth::provider('assessment-user-provider', function ($app, array $config) use ($allowedRoles) {
            return new AssessmentUserProvider($app['hash'], $config['model'], $allowedRoles);
        });

        $this->app->singleton(
            UpdatesUserPasswordContract::class,
            UpdateUserPassword::class
        );

        $this->app->singleton(
            ResetsUserPasswords::class,
            ResetUserPassword::class
        );

        $this->app->singleton(
            FortifyCompletePasswordReset::class,
            CompletePasswordReset::class
        );

        $this->app->singleton(
            SuccessfulPasswordResetLinkRequestResponseContract::class,
            SuccessfulPasswordResetLinkRequestResponse::class
        );
        $this->app->singleton(
            FailedPasswordResetLinkRequestResponseContract::class,
            SuccessfulPasswordResetLinkRequestResponse::class
        );

        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });

        Fortify::authenticateUsing(function (Request $request) {
            /** @var UserProvider $provider */
            $provider = $this->app->make(StatefulGuard::class)->getProvider();

            $user = $provider->retrieveByCredentials(['email' => $request->email]);

            if ($user && $provider->validateCredentials($user, ['password' => $request->password])) {
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
