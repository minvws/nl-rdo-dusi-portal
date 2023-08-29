<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel\Providers;

use Illuminate\Support\ServiceProvider as Base;
use MinVWS\DUSi\Shared\Bridge\Laravel\BridgeManager;
use MinVWS\DUSi\Shared\Bridge\Laravel\ConnectionManager;
use MinVWS\DUSi\Shared\Bridge\Laravel\Console\PingBridge;
use MinVWS\DUSi\Shared\Bridge\Laravel\Console\RunBridge;
use MinVWS\DUSi\Shared\Bridge\Laravel\ServerManager;

class ServiceProvider extends Base
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/bridge.php' => config_path('bridge.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([RunBridge::class]);
            $this->commands([PingBridge::class]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/bridge.php',
            'bridge'
        );

        $this->app->singleton(ConnectionManager::class);
        $this->app->singleton(ServerManager::class);
        $this->app->singleton(BridgeManager::class);
    }
}
