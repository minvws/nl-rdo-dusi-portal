<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\AesEncryption;

use Exception;
use Illuminate\Encryption\Encrypter;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;

class AesEncryptionService
{
    protected const AES_CIPHER = 'aes-256-gcm';

    public function __construct(
        private readonly HsmEncryptionService $hsmEncryptionService,
    ) {
    }

    /**
     * @return array{HsmEncryptedData, Encrypter} aes key encrypted with HSM public key
     * @throws Exception
     */
    protected function generateEncryptionKey(): array
    {
        $key = Encrypter::generateKey(self::AES_CIPHER);

        $encrypter = $this->getAesEncrypter($key);
        $encryptedKey = $this->hsmEncryptionService->encrypt($key);

        return [$encryptedKey, $encrypter];
    }

    protected function getAesEncrypter(string $aesKey): Encrypter
    {
        return new Encrypter($aesKey, self::AES_CIPHER);
    }
}
