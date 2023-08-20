<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
        Artisan::call('db:wipe', ['--database' => 'pgsql_form']);
        Artisan::call('db:wipe', ['--database' => 'pgsql_application']);
        Artisan::call('migrate:fresh');
    }


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
            $config->set('database.default', 'pgsql_application');
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
