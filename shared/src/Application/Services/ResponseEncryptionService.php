<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Exception;
use JsonException;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\Codable\Exceptions\PathNotFoundException;
use MinVWS\Codable\Exceptions\ValueNotFoundException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;

class ResponseEncryptionService
{
    public function __construct(
        private JSONEncoder $encoder,
        private JSONDecoder $decoder
    ) {
    }

    public function encrypt(
        EncryptedResponseStatus $status,
        string $payload,
        string $contentType,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $data = sodium_crypto_box_seal($payload, $publicKey->value);
        return new EncryptedResponse($status, $contentType, $data);
    }

    /**
     * Decrypt response, mainly used for testing.
     *
     * @throws Exception
     */
    public function decrypt(
        EncryptedResponse $response,
        string $keyPair
    ): string {
        $data = sodium_crypto_box_seal_open($response->data, $keyPair);
        if ($data === false) {
            throw new Exception('Decryption failed, invalid key pair?');
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function encryptCodable(
        EncryptedResponseStatus $status,
        Codable $payload,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $json = $this->encoder->encode($payload);
        return $this->encrypt($status, $json, 'application/json', $publicKey);
    }

    /**
     * Decrypt response, mainly used for testing.
     *
     * @template T of Codable
     *
     * @param class-string<T> $class
     *
     * @return Codable
     *
     * @throws JsonException
     * @throws CodableException
     * @throws PathNotFoundException
     * @throws ValueNotFoundException
     * @throws ValueTypeMismatchException
     * @throws Exception
     */
    public function decryptCodable(
        EncryptedResponse $response,
        string $class,
        string $keyPair
    ): Codable {
        $json = $this->decrypt($response, $keyPair);

        return $this->decoder->decode($json)->decodeObject($class);
    }
}
