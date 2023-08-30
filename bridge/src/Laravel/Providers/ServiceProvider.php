<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as Base;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Bridge\Laravel\BridgeManager;
use MinVWS\DUSi\Shared\Bridge\Laravel\ConnectionManager;
use MinVWS\DUSi\Shared\Bridge\Laravel\Console\PingBridge;
use MinVWS\DUSi\Shared\Bridge\Laravel\Console\RunBridge;
use MinVWS\DUSi\Shared\Bridge\Laravel\ServerManager;
use MinVWS\DUSi\Shared\Bridge\Server\Server;
use MinVWS\DUSi\Shared\Bridge\Shared\Connection;

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

        $this->app->singleton(
            Server::class,
            fn (Application $app) => $app->get(ServerManager::class)->server()
        );
        $this->app->singleton(
            Connection::class,
            fn (Application $app) => $app->get(ConnectionManager::class)->connection()
        );
        $this->app->singleton(
            Client::class,
            fn (Application $app) => $app->get(ConnectionManager::class)->client()
        );
    }
}
