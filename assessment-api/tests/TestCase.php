<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

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
}
