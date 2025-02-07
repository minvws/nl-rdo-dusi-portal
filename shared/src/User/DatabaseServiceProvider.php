<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User;

use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }


    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/user_migrations');
    }
}
