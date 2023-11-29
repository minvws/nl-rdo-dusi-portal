<?php

declare(strict_types=1);

namespace Feature\Services;

use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Shared\Tests\TestCase;

/**
 * @group surepay
 */
class SurePayServiceTest extends TestCase
{
    use WithFaker;
    use MocksEncryptionAndHashing;


    public function testCheckSurePayForApplicationShouldStoreEncryptedNameSuggestion(): void
    {

    }
}
