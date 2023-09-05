<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;

class ValidationServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function testSelectFieldRules(): void
    {
        $this->markTestSkipped('TODO: implement testSelectFieldRules()');

        // TODO: Write tests for select and multi select ... with dataprovider
    }

    public function testTextMaxLengthRule(): void
    {
        $this->markTestSkipped('TODO: implement testTextMaxLengthRule()');
    }
}
