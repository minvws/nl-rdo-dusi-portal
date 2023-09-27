<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Providers;

use GuzzleHttp\Client;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Services\FrontendDecryptionService;
use MinVWS\DUSi\Application\Backend\Services\IdentityService;
use MinVWS\DUSi\Application\Backend\Services\SurePay\SurePayService;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Services\Clamav\ClamAvService;
use RuntimeException;

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

        $this->registerClamAv();
        $this->registerSurePayService();
    }

    private function registerClamAv(): void
    {
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
    }

    public function registerSurePayService(): void
    {
        $this->app->singleton(SurePayService::class, function () {
            $config = config('surepay_api');

            if (empty($config->get('endpoint'))) {
                throw new RuntimeException(
                    'Please set the env SUREPAY_ENDPOINT to the SurePay API endpoint URL.'
                );
            }

            return new SurePayService(
                client: new Client([
                    'base_uri' => $config->get('endpoint'),
                    'verify'   => false,
                ]),
            );
        });
    }
}
