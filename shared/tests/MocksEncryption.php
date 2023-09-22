<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests;

use Illuminate\Encryption\Encrypter;
use MinVWS\DUSi\Shared\Application\Interfaces\KeyReader;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationFileEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmService;
use MinVWS\DUSi\Shared\Serialisation\Hsm\HsmDecryptableData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use Mockery;

trait MocksEncryption
{
    public function setupMocksEncryption(): void
    {
        $privateKey = openssl_pkey_new();
        $publicKeyPem = openssl_pkey_get_details($privateKey)['key'];

        $keyReader = $this->getMockBuilder(KeyReader::class)
            ->getMock();

        $hsmService = $this->getMockBuilder(HsmService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hsmService->expects($this->any())
            ->method('decrypt')
            ->willReturnCallback(function ($keyLabel, $data) use ($privateKey) {
                $result = openssl_private_decrypt($data, $decrypted, openssl_pkey_get_private($privateKey));
                if (!$result) {
                    return null;
                }
                return $decrypted;
            });

        $hsmEncryptionService = $this->getMockBuilder(HsmEncryptionService::class)
            ->setConstructorArgs([$keyReader, ''])
            ->onlyMethods(['getPublicKey'])
            ->getMock();

        $hsmEncryptionService->expects($this->any())
            ->method('getPublicKey')
            ->willReturnCallback(function () use ($publicKeyPem) {
                return openssl_pkey_get_public($publicKeyPem);
            });

        $this->app->instance(HsmEncryptionService::class, $hsmEncryptionService);

        $hsmDecryptionService = $this->getMockBuilder(HsmDecryptionService::class)
            ->setConstructorArgs([$hsmService])
            ->onlyMethods(['decrypt'])
            ->getMock();

        // Configure the decryptData method to return the same value as the input parameter
        $hsmDecryptionService->expects($this->any())
            ->method('decrypt')
            ->willReturnCallback(function (HsmDecryptableData $input) {
                return $input->getData();
            });

        $this->app->instance(HsmDecryptionService::class, $hsmDecryptionService);

        $encrypterMock = Mockery::mock(Encrypter::class);
        $encrypterMock->shouldReceive('encrypt')
            ->andReturnUsing(function ($value) {
                return $value;
            });
        $encrypterMock->shouldReceive('decrypt')
            ->andReturnUsing(function ($value) {
                return $value;
            });
        $encrypterMock->shouldReceive('encryptString')
            ->andReturnUsing(function ($value) {
                return $value;
            });
        $encrypterMock->shouldReceive('decryptString')
            ->andReturnUsing(function ($value) {
                return $value;
            });

        $applicationEncryptorMock = Mockery::mock(ApplicationStageEncryptionService::class);
        $applicationEncryptorMock
            ->shouldReceive('getEncrypter')
            ->andReturn($encrypterMock);

        $applicationEncryptorMock
            ->shouldReceive('generateEncryptionKey')
            ->andReturn([new HsmEncryptedData('', ''), $encrypterMock]);

        $applicationEncryptorMock
            ->shouldReceive('getEncrypter')
            ->andReturnUsing(function (ApplicationStage $applicationStage) use ($encrypterMock) {
                return $encrypterMock;
            });

        $this->app->instance(ApplicationStageEncryptionService::class, $applicationEncryptorMock);

        $applicationFileEncryptionService = Mockery::mock(ApplicationFileEncryptionService::class);
        $applicationFileEncryptionService
            ->shouldReceive('getEncrypter')
            ->andReturn($encrypterMock);

        $applicationFileEncryptionService
            ->shouldReceive('generateKeyInfo')
            ->andReturn([json_encode(new HsmEncryptedData('', '')), $encrypterMock]);

        $this->app->instance(ApplicationFileEncryptionService::class, $applicationFileEncryptionService);
    }
}
