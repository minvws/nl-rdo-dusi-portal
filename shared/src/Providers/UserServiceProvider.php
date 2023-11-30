<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Application\Console\Commands\CheckSurePay;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\BankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\MockedBankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\SurePayRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
use RuntimeException;

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
