<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests;

use Illuminate\Contracts\Config\Repository;
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
        Artisan::call('migrate:fresh');
    }


    public function getPackageProviders($app): array
    {
        return [
            'MinVWS\DUSi\Shared\Providers\DatabaseServiceProvider'
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
        });
    }
}
