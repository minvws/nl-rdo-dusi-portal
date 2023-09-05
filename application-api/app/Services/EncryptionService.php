<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Interfaces\KeyReader;
use MinVWS\DUSi\Application\API\Services\Exceptions\DataEncryptionException;

class EncryptionService
{
    private \OpenSSLAsymmetricKey $publicKey;
    private string $aesKey;
    private string $initializationVector;

    public function __construct(protected KeyReader $keyReader)
    {
    }

    /**
     * @throws DataEncryptionException
     */
    protected function aesEncrypt(string $value): string
    {
        $encrypted = openssl_encrypt(
            $value,
            'AES-256-CBC',
            $this->aesKey,
            OPENSSL_RAW_DATA,
            $this->initializationVector
        );
        if ($encrypted === false) {
            throw new DataEncryptionException("Could not encrypt data");
        }
        return $encrypted;
    }

    /**
     * @throws DataEncryptionException
     */
    protected function rsaEncrypt(string $encrypted): string
    {
        openssl_public_encrypt(
            $this->aesKey,
            $aesEncrypted,
            $this->publicKey,
            OPENSSL_PKCS1_OAEP_PADDING
        );

        $dataToBeSavedInDatabase = json_encode([
            "encrypted" =>      base64_encode($encrypted),
            "encrypted_aes" =>  base64_encode($aesEncrypted),
            "iv" =>             base64_encode($this->initializationVector),
        ]);
        if ($dataToBeSavedInDatabase === false) {
            throw new DataEncryptionException("Could not encode data");
        }
        return base64_encode($dataToBeSavedInDatabase);
    }


    /**
     * @throws DataEncryptionException
     */
    public function encryptData(string $value): string
    {
        $cert = $this->keyReader->getKey();
        $this->publicKey = openssl_pkey_get_public($cert) ?:
            throw new DataEncryptionException("Could not get public key");


        try {
            $this->aesKey = random_bytes(32);
            $this->initializationVector = random_bytes(16);
        } catch (\Exception $e) {
            throw new DataEncryptionException($e->getMessage());
        }

        $aesEncrypted = $this->aesEncrypt($value);
        $rsaEncrypted = $this->rsaEncrypt($aesEncrypted);
        return $rsaEncrypted;
    }
}
