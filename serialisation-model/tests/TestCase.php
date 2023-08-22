<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Tests;

use Mockery;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

}
