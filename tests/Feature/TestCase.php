<?php


namespace MinVWS\DUSi\Shared\Application\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    public function getPackageProviders($app): array
    {
        return [
            'MinVWS\DUSi\Shared\Application\ApplicationServiceProvider',
            'MinVWS\DUSi\Shared\Subsidy\SubsidyServiceProvider',
        ];
    }

    public function defineEnvironment($app): void
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'pgsql_form');
            $config->set('database.connections.pgsql_form', [
                'driver' => 'pgsql',
                'host' => env('DB_FORM_HOST'),
                'port' => env('DB_FORM_PORT'),
                'database' => env('DB_FORM_DATABASE'),
                'username' => env('DB_FORM_USERNAME'),
                'password' => env('DB_FORM_PASSWORD'),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]);
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