<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

class TestCase extends BaseTestCase
{
    use CreatesApplication;
//    use RefreshDatabase;

    protected function SetUp(): void
    {
        parent::setUp();
//        $this->loadCustomMigrations();
    }

    protected function loadCustomMigrations(): void
    {
        Artisan::call('db:wipe', ['--database' => 'pgsql_application']);
        Artisan::call('migrate:fresh');
//        Artisan::call('migrate:fresh', ['--path' => 'vendor/minvws/dusi-subsidy-model/database/migrations']);
//        Artisan::call('migrate:fresh', ['--path' => 'vendor/minvws/dusi-application-model/database/migrations']);
    }
}
