<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();
    }

    protected function loadCustomMigrations(): void
    {
        Artisan::call('db:wipe', ['--database' => 'pgsql_application']);
        Artisan::call('db:wipe', ['--database' => 'pgsql_user']);

        Artisan::call('migrate:fresh');
    }


    public function getPackageProviders($app): array
    {
        return [
            'MinVWS\DUSi\Shared\Providers\DatabaseServiceProvider',
            'MinVWS\DUSi\Shared\User\DatabaseServiceProvider',
            'MinVWS\DUSi\Shared\Providers\SurePayServiceProvider',
        ];
    }

    public function defineEnvironment($app): void
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'pgsql_application');
            $config->set('database.connections.pgsql_application', [
                'driver' => 'pgsql',
                'host' => env('DB_APPLICATION_HOST'),
                'port' => env('DB_APPLICATION_PORT'),
                'database' => env('DB_APPLICATION_DATABASE'),
                'username' => env('DB_APPLICATION_USERNAME'),
                'password' => env('DB_APPLICATION_PASSWORD'),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]);

            $config->set('database.connections.pgsql_user', [
                'driver' => 'pgsql',
                'host' => env('DB_USER_HOST'),
                'port' => env('DB_USER_PORT'),
                'database' => env('DB_USER_DATABASE'),
                'username' => env('DB_USER_USERNAME'),
                'password' => env('DB_USER_PASSWORD'),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]);
            $config->set('database.dbal', ['types' => ['timestamp' => TimestampType::class]]);
        });
    }
}
