<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests;

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
        Artisan::call('migrate:fresh', ['--path' => 'vendor/minvws/dusi-subsidy-model/database/migrations']);
    }
}
