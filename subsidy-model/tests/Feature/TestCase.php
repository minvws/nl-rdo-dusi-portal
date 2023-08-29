<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    public function getPackageProviders($app)
    {
        return [
            'MinVWS\DUSi\Shared\Subsidy\SubsidyServiceProvider',
        ];
    }

    public function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'pgsql_application');
            $config->set('database.connections.pgsql_application', [
                'driver' => 'pgsql',
                'url' => env('DATABASE_FORM_URL'),
                'host' => env('DB_APPLICATION_HOST', '127.0.0.1'),
                'port' => env('DB_APPLICATION_PORT', '5432'),
                'database' => env('DB_APPLICATION_DATABASE', 'forge'),
                'username' => env('DB_APPLICATION_USERNAME', 'forge'),
                'password' => env('DB_APPLICATION_PASSWORD', ''),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]);
        });
    }
}
