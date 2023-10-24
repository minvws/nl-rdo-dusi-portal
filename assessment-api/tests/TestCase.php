<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use MinVWS\DUSi\Shared\User\Models\Connection as UserConnection;
use MinVWS\DUSi\Shared\Application\Models\Connection as ApplicationConnection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
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
                '--path' => 'vendor/minvws/dusi-shared/database/user_migrations',
            ]);
            $this->artisan('migrate:fresh', [
                '--database' => ApplicationConnection::APPLICATION,
                '--path' => 'vendor/minvws/dusi-shared/database/migrations',
            ]);

            RefreshDatabaseState::$migrated = true;
        }
    }
}
