<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\AesEncryption;

use Closure;
use Exception;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Encryption\Encrypter;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;

class ApplicationStageEncryptionMockService extends ApplicationStageEncryptionService
{
    public function __construct(
        readonly Closure $createEncryptor,
        HsmEncryptionService $hsmEncryptionService,
        HsmDecryptionService $hsmDecryptionService
    ) {
        parent::__construct($hsmEncryptionService, $hsmDecryptionService);
    }

    /**
     * @return array{HsmEncryptedData, EncrypterContract} aes key encrypted with HSM public key
     * @throws Exception
     */
    public function generateEncryptionKey(): array
    {
        return parent::generateEncryptionKey();
    }

    public function getEncrypter(ApplicationStage $applicationStage): EncrypterContract
    {
        return $this->createEncryptor->call($this, $applicationStage->encrypted_key->keyLabel);
    }
}
