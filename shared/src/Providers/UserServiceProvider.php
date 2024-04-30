<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/user.php' => config_path('user.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/user.php',
            'user'
        );
    }
}
