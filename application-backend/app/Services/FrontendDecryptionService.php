<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Exception;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionMissingKeyPairException;
use SodiumException;

class FrontendDecryptionService implements FrontendDecryption
{
    /**
     * @var string Sodium key pair
     */
    protected string $keyPair;

    /**
     * @param string $publicKey Base64 encoded public key
     * @param string $privateKey Base64 encoded private key
     * @throws FrontendDecryptionMissingKeyPairException
     */
    public function __construct(protected string $publicKey, protected string $privateKey)
    {
        if (empty($this->publicKey) || empty($this->privateKey)) {
            throw new FrontendDecryptionMissingKeyPairException(message: 'Public and private key must not be empty');
        }

        try {
            $this->keyPair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                base64_decode($this->privateKey),
                base64_decode($this->publicKey)
            );
        } catch (Exception $e) {
            throw new FrontendDecryptionMissingKeyPairException(
                message: 'Exception while sodium_crypto_box_keypair_from_secretkey_and_publickey',
                previous: $e
            );
        }
    }

    /**
     * @param string $encryptedData Base64 encoded sodium encrypted data
     * @return string Decrypted data
     * @throws FrontendDecryptionFailedException
     */
    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData, true);
        if ($data === false) {
            throw new FrontendDecryptionFailedException('Could not base64_decode data');
        }

        try {
            $decryptedData = sodium_crypto_box_seal_open($data, $this->keyPair);
        } catch (SodiumException $e) {
            throw new FrontendDecryptionFailedException(
                message: 'Could not decrypt data',
                previous: $e
            );
        }
        if ($decryptedData === false) {
            throw new FrontendDecryptionFailedException(message: 'Could not decrypt data');
        }

        return $decryptedData;
    }
}
