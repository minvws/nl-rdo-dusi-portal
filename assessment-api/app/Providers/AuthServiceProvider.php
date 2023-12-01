<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use MinVWS\DUSi\Assessment\API\Policies\ApplicationPolicy;
use MinVWS\DUSi\Assessment\API\Policies\SubidyPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \MinVWS\DUSi\Shared\Application\Models\Application::class => ApplicationPolicy::class,
        \MinVWS\DUSi\Shared\Subsidy\Models\Subsidy::class => SubidyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
