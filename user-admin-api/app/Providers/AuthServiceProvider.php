<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\User\Admin\API\Policies\OrganisationPolicy;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\User\Admin\API\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Organisation::class => OrganisationPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
