<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Encryption\Encrypter;
use MinVWS\DUSi\Assessment\API\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

class ApplicationEncryptionService
{
    protected const AES_CIPHER = 'aes-256-gcm';

    public function __construct(protected HsmEncryptionService $hsmEncryptionService)
    {
        // TODO: Move to Shared package
    }

    public function getEncrypter(ApplicationStage $applicationStage): EncrypterContract
    {
        $aesKey = $this->hsmEncryptionService->decrypt($applicationStage->encrypted_key);

        return $this->getAesEncrypter($aesKey);
    }

    protected function getAesEncrypter(string $aesKey): EncrypterContract
    {
        return new Encrypter($aesKey, self::AES_CIPHER);
    }
}
