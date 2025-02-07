<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use MinVWS\DUSi\Shared\Application\Models\Connection as ApplicationConnection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected array $connectionsToTransact = [
        ApplicationConnection::APPLICATION,
    ];

    public function runDatabaseMigrations(): void
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', [
                '--database' => ApplicationConnection::APPLICATION,
                '--path' => 'vendor/minvws/dusi-shared/database/migrations',
            ]);

            RefreshDatabaseState::$migrated = true;
        }
    }
}
