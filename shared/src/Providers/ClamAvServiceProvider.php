<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Services\ClamAv\ClamAvService;

class ClamAvServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/clamav.php' => config_path('clamav.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/clamav.php',
            'clamav'
        );

        $this->app->when(ClamAvService::class)
            ->needs('$enabled')
            ->giveConfig('clamav.enabled');
        $this->app->when(ClamAvService::class)
            ->needs('$preferredSocket')
            ->giveConfig('clamav.preferred_socket');
        $this->app->when(ClamAvService::class)
            ->needs('$unixSocket')
            ->giveConfig('clamav.unix_socket');
        $this->app->when(ClamAvService::class)
            ->needs('$tcpSocket')
            ->giveConfig('clamav.tcp_socket');
        $this->app->when(ClamAvService::class)
            ->needs('$socketConnectTimeout')
            ->giveConfig('clamav.socket_connect_timeout');
        $this->app->when(ClamAvService::class)
            ->needs('$socketReadTimeout')
            ->giveConfig('clamav.socket_read_timeout');
        $this->app->when(ClamAvService::class)
            ->needs('$setFilePermissionsBeforeScan')
            ->giveConfig('clamav.set_file_permissions_before_scan');
    }
}
