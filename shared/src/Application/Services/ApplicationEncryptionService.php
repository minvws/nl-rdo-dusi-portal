<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Exception;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Encryption\Encrypter;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;

class ApplicationEncryptionService
{
    protected const AES_CIPHER = 'aes-256-gcm';

    public function __construct(protected HsmEncryptionService $hsmEncryptionService)
    {
    }

    public function getEncrypter(ApplicationStage $applicationStage): EncrypterContract
    {
        $aesKey = $this->hsmEncryptionService->decrypt($applicationStage->encrypted_key);

        return $this->getAesEncrypter($aesKey);
    }

    /**
     * @return array{HsmEncryptedData, EncrypterContract} aes key encrypted with HSM public key
     * @throws Exception
     */
    public function generateEncryptionKey(): array
    {
        $key = Encrypter::generateKey(self::AES_CIPHER);

        $encrypter = $this->getAesEncrypter($key);
        $encryptedKey = $this->hsmEncryptionService->encrypt($key);

        return [$encryptedKey, $encrypter];
    }

    protected function getAesEncrypter(string $aesKey): EncrypterContract
    {
        return new Encrypter($aesKey, self::AES_CIPHER);
    }
}
