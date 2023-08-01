<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Auth\PortalUserGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend(
            'oidc', function ($app, $name, array $config) {
                return new PortalUserGuard($app->make('session')->driver());
            }
        );
    }
}
