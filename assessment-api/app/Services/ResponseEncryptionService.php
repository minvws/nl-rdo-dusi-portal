<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Exception;

class ResponseEncryptionService
{
    // TODO: Move to Shared package

    public function encrypt(
        string $payload,
        string $publicKey,
    ): string {
        $data = sodium_crypto_box_seal($payload, $publicKey);
        return base64_encode($data);
    }

    /**
     * Decrypt response, mainly used for testing.
     *
     * @throws Exception
     */
    public function decrypt(
        string $response,
        string $keyPair
    ): string {
        $data = sodium_crypto_box_seal_open($response, $keyPair);
        if ($data === false) {
            throw new Exception('Decryption failed, invalid key pair?');
        }

        return $data;
    }
}
