<?php

namespace MinVWS\DUSi\Shared\Tests\Trait;

use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Hsm\HsmDecryptableData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use Mockery;

trait HsmEncryptionMockTrait
{
    protected function getHsmEncryptionServiceMock(): HsmEncryptionService
    {
        $mock = Mockery::mock(HsmEncryptionService::class);
        $mock->shouldReceive('encrypt')
            ->andReturnUsing(function (string $data) {
                return new HsmEncryptedData(base64_encode($data), 'some-label');
            });

        return $mock;
    }

    protected function getHsmDecryptionServiceMock(): HsmDecryptionService
    {
        $mock = Mockery::mock(HsmDecryptionService::class);
        $mock->shouldReceive('decrypt')
            ->andReturnUsing(function (HsmDecryptableData $data) {
                return base64_decode($data->getData());
            });

        return $mock;
    }
}
