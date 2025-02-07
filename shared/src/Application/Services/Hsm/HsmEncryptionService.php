<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Hsm;

use Exception;
use MinVWS\DUSi\Shared\Application\Interfaces\KeyReader;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use OpenSSLAsymmetricKey;

class HsmEncryptionService
{
    private ?OpenSSLAsymmetricKey $publicKey = null;

    public function __construct(
        protected KeyReader $publicKeyReader,
        protected string $hsmEncryptionKeyLabel,
    ) {
    }

    public function getEncryptionKeyLabel(): string
    {
        return $this->hsmEncryptionKeyLabel;
    }

    /**
     * @throws Exception
     */
    protected function getPublicKey(): OpenSSLAsymmetricKey
    {
        if ($this->publicKey instanceof OpenSSLAsymmetricKey) {
            return $this->publicKey;
        }

        $file = $this->publicKeyReader->getKey();

        $publicKey = openssl_pkey_get_public($file) ;
        if (!($publicKey instanceof OpenSSLAsymmetricKey)) {
            throw new Exception("Could not load hsm public key");
        }

        $this->publicKey = $publicKey;
        return $this->publicKey;
    }

    /**
     * @throws Exception
     */
    public function encrypt(string $data): HsmEncryptedData
    {
        $publicKey = $this->getPublicKey();

        $success = openssl_public_encrypt(
            $data,
            $encrypted,
            $publicKey,
            OPENSSL_PKCS1_OAEP_PADDING
        );
        if ($success === false) {
            throw new Exception("Could not encrypt data with hsm public key");
        }

        return new HsmEncryptedData(
            data: base64_encode($encrypted),
            keyLabel: $this->getEncryptionKeyLabel(),
        );
    }
}
