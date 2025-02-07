<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Providers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Services\FrontendDecryptionService;
use MinVWS\DUSi\Application\Backend\Services\IdentityService;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationFileRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->when(IdentityService::class)->needs('$hashSecret')
            ->giveConfig('identity.hash_secret');
        $this->app->when(IdentityService::class)->needs('$hashAlgorithm')
            ->giveConfig('identity.hash_algorithm');

        $this->app->when(ApplicationFileRepository::class)
            ->needs(Filesystem::class)
            ->give(function (Application $app) {
                return $app->make(FilesystemManager::class)->disk(Disk::APPLICATION_FILES);
            });

        $this->app->bind(FrontendDecryption::class, FrontendDecryptionService::class);
        $this->app->when(FrontendDecryptionService::class)
            ->needs('$publicKey')
            ->giveConfig('frontend.form_encryption.public_key');
        $this->app->when(FrontendDecryptionService::class)
            ->needs('$privateKey')
            ->giveConfig('frontend.form_encryption.private_key');
    }
}
