<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Tests\Unit;

use MinVWS\DUSi\Shared\Serialisation\Handlers\FileUploadHandlerInterface;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use Mockery;
use MinVWS\DUSi\Shared\Serialisation\Tests\TestCase;

class ProcessFileUploadTest extends TestCase
{
    public function testHandleCallsInterface()
    {
        $fileUpload = new FileUpload(
            new Identity(
                IdentityType::EncryptedCitizenServiceNumber,
                '123456789'
            ),
            new ApplicationMetadata(
                'applicationStageId',
                'subsidyStageId',
            ),
            'fieldCode',
            'id',
            'mimeType',
            'extension',
            'encryptedContents',
        );

        $fileUploadHandler = Mockery::mock(FileUploadHandlerInterface::class, function ($mock) use ($fileUpload) {
            $mock->expects('handle')->with($fileUpload)->once();
        });

        $processFileUpload = new ProcessFileUpload($fileUpload);

        $processFileUpload->handle($fileUploadHandler);
    }
}
