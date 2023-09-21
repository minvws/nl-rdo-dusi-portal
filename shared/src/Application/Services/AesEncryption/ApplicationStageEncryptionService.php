<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\AesEncryption;

use Exception;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;

readonly class ApplicationStageEncryptionService extends AesEncryptionService
{
    public function __construct(
        HsmEncryptionService $hsmEncryptionService,
        private HsmDecryptionService $hsmDecryptionService
    ) {
        parent::__construct($hsmEncryptionService);
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
        $aesKey = $this->hsmDecryptionService->decrypt($applicationStage->encrypted_key);

        return $this->getAesEncrypter($aesKey);
    }
}
