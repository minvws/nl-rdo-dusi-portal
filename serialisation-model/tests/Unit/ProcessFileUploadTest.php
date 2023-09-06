<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Tests\Unit;

use MinVWS\DUSi\Shared\Serialisation\Handlers\FileUploadHandlerInterface;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use Mockery;
use MinVWS\DUSi\Shared\Serialisation\Tests\TestCase;

class ProcessFileUploadTest extends TestCase
{
    public function testHandleCallsInterface()
    {
        $fileUpload = new FileUpload(
            new EncryptedIdentity(
                IdentityType::EncryptedCitizenServiceNumber,
                random_bytes(100)
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
