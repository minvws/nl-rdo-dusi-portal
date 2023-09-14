<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests;

use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Interfaces\KeyReader;
use MinVWS\DUSi\Application\Backend\Repositories\IdentityRepository;
use MinVWS\DUSi\Application\Backend\Services\EncryptionService;
use MinVWS\DUSi\Application\Backend\Services\Hsm\HsmService;
use MinVWS\DUSi\Application\Backend\Services\IdentityService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use Mockery;

trait MocksEncryptionAndHashing
{
    public function withoutFrontendEncryption(): void
    {
        $frontendDecryption = Mockery::mock(FrontendDecryption::class);
        $frontendDecryption->shouldReceive('decrypt')
            ->andReturnUsing(function ($input) {
                return $input;
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
        $keyReader = $this->getMockBuilder(KeyReader::class)
            ->getMock();

        $hsmService = $this->getMockBuilder(HsmService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $encryptionServiceMock = $this->getMockBuilder(EncryptionService::class)
            ->setConstructorArgs([$keyReader, $hsmService])
            ->onlyMethods(['decryptBase64EncodedData', 'decryptData', 'encryptData'])
            ->getMock();

        // Configure the decryptData method to return the same value as the input parameter
        $encryptionServiceMock->expects($this->any())
            ->method('decryptBase64EncodedData')
            ->willReturnCallback(function ($input) {
                return $input;
            });

        $encryptionServiceMock->expects($this->any())
            ->method('decryptData')
            ->willReturnCallback(function ($input) {
                return $input;
            });

        $encryptionServiceMock->expects($this->any())
            ->method('encryptData')
            ->willReturnCallback(function ($input) {
                return $input;
            });

        $this->app->instance(EncryptionService::class, $encryptionServiceMock);

        $identityServiceMock = $this->getMockBuilder(IdentityService::class)
            ->setConstructorArgs([$this->app->get(IdentityRepository::class), $encryptionServiceMock, ''])
            ->onlyMethods(['hashIdentifier'])
            ->getMock();

        $identityServiceMock->expects($this->any())
            ->method('hashIdentifier')
            ->willReturnCallback(function ($type, $identifier) {
                return $identifier;
            });

        $this->app->instance(IdentityService::class, $identityServiceMock);
    }
}
