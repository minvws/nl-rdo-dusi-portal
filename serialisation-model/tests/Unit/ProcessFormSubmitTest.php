<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Tests\Unit;

use MinVWS\DUSi\Shared\Serialisation\Handlers\FormSubmitHandlerInterface;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Tests\TestCase;
use Mockery;

class ProcessFormSubmitTest extends TestCase
{
    public function testHandleCallsInterface()
    {
        $formSubmit = new FormSubmit(
            new EncryptedIdentity(
                IdentityType::CitizenServiceNumber,
                random_bytes(100)
            ),
            new ApplicationMetadata(
                'applicationId',
                'subsidyStageId',
            ),
            'encryptedData'
        );

        $formSubmitHandler = Mockery::mock(FormSubmitHandlerInterface::class, function ($mock) use ($formSubmit) {
            $mock->expects('handle')->with($formSubmit)->once();
        });

        $processFormSubmit = new ProcessFormSubmit($formSubmit);

        $processFormSubmit->handle($formSubmitHandler);
    }
}
