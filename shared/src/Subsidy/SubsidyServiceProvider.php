<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy;

use Illuminate\Support\ServiceProvider;

class SubsidyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }


    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
