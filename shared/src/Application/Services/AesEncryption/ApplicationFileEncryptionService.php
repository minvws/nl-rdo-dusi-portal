<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\AesEncryption;

use Exception;
use Illuminate\Contracts\Encryption\StringEncrypter as EncrypterContract;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;

class ApplicationFileEncryptionService extends AesEncryptionService
{
    public function __construct(
        readonly HsmEncryptionService $hsmEncryptionService,
        private readonly HsmDecryptionService $hsmDecryptionService,
        private readonly JSONDecoder $jsonDecoder,
        private readonly JSONEncoder $jsonEncoder,
    ) {
        parent::__construct($hsmEncryptionService);
    }

    public function getEncrypter(string $keyInfo): EncrypterContract
    {
        $hsmEncryptedData = $this->jsonDecoder->decode($keyInfo)->decodeObject(HsmEncryptedData::class);

        $aesKey = $this->hsmDecryptionService->decrypt($hsmEncryptedData);

        return $this->getAesEncrypter($aesKey);
    }

    /**
     * @return array{string, EncrypterContract} aes key encrypted with HSM public key
     * @throws Exception
     */
    public function generateKeyInfo(): array
    {
        [$encryptedKey, $encrypter] = $this->generateEncryptionKey();

        $json = $this->jsonEncoder->encode($encryptedKey);

        return [$json, $encrypter];
    }
}
