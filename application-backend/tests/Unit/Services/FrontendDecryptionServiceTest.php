<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services;

use Generator;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionMissingKeyPairException;
use MinVWS\DUSi\Application\Backend\Services\FrontendDecryptionService;
use PHPUnit\Framework\TestCase;
use Throwable;

class FrontendDecryptionServiceTest extends TestCase
{
    /**
     * @var string The public key of the key pair in base64 encoding
     */
    protected string $publicKey;

    /**
     * @var string The private key of the key pair in base64 encoding
     */
    protected string $privateKey;

    /**
     * @var string The key pair
     */
    protected string $keyPair;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a key pair and set the public and private keys
        $this->keyPair = sodium_crypto_box_keypair();
        $this->publicKey = base64_encode(sodium_crypto_box_publickey($this->keyPair));
        $this->privateKey = base64_encode(sodium_crypto_box_secretkey($this->keyPair));
    }

    public function testServiceCanBeCreated(): void
    {
        $service = new FrontendDecryptionService(
            publicKey: $this->publicKey,
            privateKey: $this->privateKey
        );

        $this->assertInstanceOf(FrontendDecryptionService::class, $service);
    }

    public static function missingKeyPairProvider(): Generator
    {
        yield ['', '', FrontendDecryptionMissingKeyPairException::class];
        yield ['public', '', FrontendDecryptionMissingKeyPairException::class];
        yield ['', 'private', FrontendDecryptionMissingKeyPairException::class];
        yield ['public', 'private', FrontendDecryptionMissingKeyPairException::class];
    }

    /**
     * @dataProvider missingKeyPairProvider
     * @param mixed $publicKey
     * @param mixed $privateKey
     * @param class-string<Throwable> $exception
     * @return void
     * @throws FrontendDecryptionMissingKeyPairException
     */
    public function testServiceCannotBeCreated(mixed $publicKey, mixed $privateKey, string $exception): void
    {
        $this->expectException($exception);

        $service = new FrontendDecryptionService(
            publicKey: $publicKey,
            privateKey: $privateKey
        );
    }

    public function testCanDecrypt(): void
    {
        $service = new FrontendDecryptionService(
            publicKey: $this->publicKey,
            privateKey: $this->privateKey
        );

        $encryptedData = sodium_crypto_box_seal('test', sodium_crypto_box_publickey($this->keyPair));
        $decryptedData = $service->decrypt(sodium_bin2base64($encryptedData, SODIUM_BASE64_VARIANT_ORIGINAL));

        $this->assertEquals('test', $decryptedData);
    }

    public function testCannotDecryptWithWrongKeyPair(): void
    {
        $service = new FrontendDecryptionService(
            publicKey: $this->publicKey,
            privateKey: $this->privateKey
        );

        $keyPair = sodium_crypto_box_keypair();
        $encryptedData = sodium_crypto_box_seal('test', sodium_crypto_box_publickey($keyPair));

        $this->expectException(FrontendDecryptionFailedException::class);
        $this->expectExceptionMessage('Could not decrypt data');

        $service->decrypt(sodium_bin2base64($encryptedData, SODIUM_BASE64_VARIANT_ORIGINAL));
    }

    public function testCannotDecryptWithoutBase64EncodedData(): void
    {
        $service = new FrontendDecryptionService(
            publicKey: $this->publicKey,
            privateKey: $this->privateKey
        );

        $this->expectException(FrontendDecryptionFailedException::class);
        $this->expectExceptionMessage('Could not decrypt data');

        $service->decrypt('#$%&*()');
    }

    public function testCannotDecryptWithoutEncryptedData(): void
    {
        $service = new FrontendDecryptionService(
            publicKey: $this->publicKey,
            privateKey: $this->privateKey
        );

        $this->expectException(FrontendDecryptionFailedException::class);
        $this->expectExceptionMessage('Could not decrypt data');

        $service->decrypt(sodium_bin2base64('this is not encrypted data', SODIUM_BASE64_VARIANT_ORIGINAL));
    }
}
