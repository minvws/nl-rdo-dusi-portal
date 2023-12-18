<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\AesEncryption;

use Exception;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Encryption\Encrypter;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;

class ApplicationStageEncryptionMockService extends ApplicationStageEncryptionService
{
    /**
     * @return array{HsmEncryptedData, EncrypterContract} aes key encrypted with HSM public key
     * @throws Exception
     */
    public function generateEncryptionKey(): array
    {
        $key = Encrypter::generateKey(self::AES_CIPHER);

        $encryptedKey = $this->hsmEncryptionService->encrypt($key);
        $encrypter = new EncrypterMock($encryptedKey->data);

        return [$encryptedKey, $encrypter];
    }

    public function getEncrypter(ApplicationStage $applicationStage): EncrypterContract
    {
        return new EncrypterMock($applicationStage->encrypted_key->data);
    }
}
