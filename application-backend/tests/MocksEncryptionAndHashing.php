<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests;

use Illuminate\Encryption\Encrypter;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Repositories\IdentityRepository;
use MinVWS\DUSi\Shared\Application\Interfaces\KeyReader;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationFileEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmService;
use MinVWS\DUSi\Application\Backend\Services\IdentityService;
use MinVWS\DUSi\Application\Backend\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Hsm\HsmDecryptableData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use Mockery;

trait MocksEncryptionAndHashing
{
    public function withoutFrontendEncryption(): void
    {
        $frontendDecryption = Mockery::mock(FrontendDecryption::class);
        $frontendDecryption->shouldReceive('decrypt')
            ->andReturnUsing(function ($input) {
                return $input instanceof BinaryData ? $input->data : $input;
            });
        $frontendDecryption->shouldReceive('decryptCodable')
            ->andReturnUsing(function ($input, $class) {
                return
                    (new JSONDecoder())
                        ->decode($input instanceof BinaryData ? $input->data : $input)
                        ->decodeObject($class);
            });
        $this->app->instance(FrontendDecryption::class, $frontendDecryption);
    }

    public function setupMocksEncryptionAndHashing(): void
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


        $applicationFileEncryptorMock = Mockery::mock(ApplicationFileEncryptionService::class);
        $applicationFileEncryptorMock
            ->shouldReceive('getEncrypter')
            ->andReturn($encrypterMock);

        $applicationFileEncryptorMock
            ->shouldReceive('generateKeyInfo')
            ->andReturn(['{}', $encrypterMock]);

        $applicationFileEncryptorMock
            ->shouldReceive('getEncrypter')
            ->andReturnUsing(function (string $keyInfo) use ($encrypterMock) {
                return $encrypterMock;
            });

        $this->app->instance(ApplicationFileEncryptionService::class, $applicationFileEncryptorMock);

        $identityServiceMock = $this->getMockBuilder(IdentityService::class)
            ->setConstructorArgs([$this->app->get(IdentityRepository::class), $hsmDecryptionService, ''])
            ->onlyMethods(['hashIdentifier'])
            ->getMock();

        $identityServiceMock->expects($this->any())
            ->method('hashIdentifier')
            ->willReturnCallback(function ($type, $identifier) {
                return $identifier;
            });

        $this->app->instance(IdentityService::class, $identityServiceMock);

        $frontendDecryptionMock = Mockery::mock(FrontendDecryption::class);
        $frontendDecryptionMock->shouldReceive('decrypt')
            ->andReturnUsing(function ($input) {
                return $input;
            });


        $this->app->instance(FrontendDecryption::class, $frontendDecryptionMock);

        $responseEncryptionServiceMock = $this
            ->getMockBuilder(ResponseEncryptionService::class)
            ->setConstructorArgs([
                $this->app->get(JSONEncoder::class),
                $this->app->get(JSONDecoder::class),
            ])
            ->onlyMethods(['encrypt', 'decrypt'])
            ->getMock();

        $responseEncryptionServiceMock
            ->expects($this->any())
            ->method('encrypt')
            ->willReturnCallback(function ($status, $payload, $contentType, $publicKey) {
                return new EncryptedResponse($status, $contentType, $payload);
            });

        $responseEncryptionServiceMock
            ->expects($this->any())
            ->method('decrypt')
            ->willReturnCallback(function ($response, $keyPair) {
                return $response->data;
            });

        $this->app->instance(ResponseEncryptionService::class, $responseEncryptionServiceMock);
    }
}
