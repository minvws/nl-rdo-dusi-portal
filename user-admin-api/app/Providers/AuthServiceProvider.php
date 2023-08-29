<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use MinVWS\DUSi\User\Admin\API\Models\Organisation;
use MinVWS\DUSi\User\Admin\API\Policies\OrganisationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Organisation::class => OrganisationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
