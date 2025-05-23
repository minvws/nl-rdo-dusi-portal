<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();
    }
}
