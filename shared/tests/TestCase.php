<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Orchestra\Testbench\TestCase as BaseTestCase;
use MinVWS\DUSi\Shared\User\Models\Connection as UserConnection;
use MinVWS\DUSi\Shared\Application\Models\Connection as ApplicationConnection;

class TestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected array $connectionsToTransact = [
        UserConnection::USER,
        ApplicationConnection::APPLICATION,
    ];

    public function runDatabaseMigrations(): void
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', [
                '--database' => UserConnection::USER,
                '--path' => 'database/user_migrations',
                '--realpath' => true,
            ]);
            $this->artisan('migrate:fresh', [
                '--database' => ApplicationConnection::APPLICATION,
                '--path' => 'database/migrations',
                '--realpath' => true,
            ]);

            RefreshDatabaseState::$migrated = true;
        }
    }


    public function getPackageProviders($app): array
    {
        return [
            'MinVWS\DUSi\Shared\Providers\DatabaseServiceProvider',
            'MinVWS\DUSi\Shared\User\DatabaseServiceProvider',
        ];
    }

    public function defineEnvironment($app): void
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', ApplicationConnection::APPLICATION);
            $config->set('database.connections.' . ApplicationConnection::APPLICATION, [
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

            $config->set('database.connections.' . UserConnection::USER, [
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
