<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Console\Commands\Hsm\HsmInfoCommand;
use MinVWS\DUSi\Shared\Application\Console\Commands\Hsm\HsmLocalClearCommand;
use MinVWS\DUSi\Shared\Application\Console\Commands\Hsm\HsmLocalInitCommand;
use MinVWS\DUSi\Shared\Application\Interfaces\KeyReader;
use MinVWS\DUSi\Shared\Application\Services\FileKeyReader;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HsmEncryptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/hsm_encryption.php' => config_path('hsm_encryption.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/hsm_encryption.php',
            'hsm_encryption'
        );

        $this->app->when(FileKeyReader::class)
            ->needs('$publicKeyPath')
            ->giveConfig('hsm_encryption.public_key_path');

        $this->app->singleton(KeyReader::class, FileKeyReader::class);

        $this->app->when(HsmEncryptionService::class)
            ->needs('$hsmEncryptionKeyLabel')
            ->giveConfig('hsm_encryption.key_label');
    }
}
