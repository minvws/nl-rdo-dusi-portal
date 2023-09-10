<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Config;
use Exception;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Interfaces\KeyReader;
use MinVWS\DUSi\Application\Backend\Services\Hsm\HsmService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use OpenSSLAsymmetricKey;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EncryptionService
{
    private OpenSSLAsymmetricKey $publicKey;
    private string $aesKey;
    private string $initializationVector;

    public function __construct(protected KeyReader $keyReader, protected HsmService $hsmService)
    {
    }

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
            throw new Exception("Could not encrypt data");
        }
        return $encrypted;
    }

    /**
     * @throws Exception
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
            throw new Exception("Could not encode data");
        }
        return base64_encode($dataToBeSavedInDatabase);
    }

    /**
     * @throws Exception
     */
    public function encryptData(string $value): string
    {
        $cert = $this->keyReader->getKey();
        $this->publicKey = openssl_pkey_get_public($cert) ?:
            throw new Exception("Could not get public key");

        try {
            $this->aesKey = random_bytes(32);
            $this->initializationVector = random_bytes(16);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $aesEncrypted = $this->aesEncrypt($value);
        $rsaEncrypted = $this->rsaEncrypt($aesEncrypted);

        return $rsaEncrypted;
    }

    protected function decryptAesKey(string $encryptedAes): string
    {
        $module = Config::get('hsm_api.module');
        $slot = Config::get('hsm_api.slot');

        return $this->hsmService->decryptHsm(
            $module,
            $slot,
            Config::get('hsm_api.encryption_key_label'),
            $encryptedAes
        );
    }

    /**
     * @throws Exception
     */
    protected function decryptAesEncrypted(
        string $encrypted,
        string $aesKeyDecrypted,
        string $initializationVector
    ): string {
        $initializationVector = base64_decode($initializationVector);
        $encrypted = base64_decode($encrypted);

        $aesDecrypted = openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $aesKeyDecrypted,
            OPENSSL_RAW_DATA,
            $initializationVector
        );

        if ($aesDecrypted === false) {
            throw new Exception("Could not decrypt data");
        }

        return $aesDecrypted;
    }

    /**
     * @throws Exception
     */
    public function decryptBase64EncodedData(string $encryptedData): string
    {
        return $this->decryptData(base64_decode($encryptedData));
    }

    /**
     * @throws Exception
     */
    public function decryptData(string $encryptedData): string
    {
        $dataArray = json_decode($encryptedData, true);

        $aesKeyDecrypted = $this->decryptAesKey($dataArray['encrypted_aes']);

        return $this->decryptAesEncrypted($dataArray['encrypted'], $aesKeyDecrypted, $dataArray['iv']);
    }

    public function encryptResponse(
        EncryptedResponseStatus $status,
        string $payload,
        string $contentType,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $data = sodium_crypto_box_seal($payload, $publicKey->value);
        return new EncryptedResponse($status, $contentType, $data);
    }

    /**
     * @throws Exception
     */
    public function encryptCodableResponse(
        EncryptedResponseStatus $status,
        Codable $payload,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $encoder = new JSONEncoder();
        $json = $encoder->encode($payload);
        return $this->encryptResponse($status, $json, 'application/json', $publicKey);
    }

    public function encryptErrorResponse(
        EncryptedResponseStatus $status,
        string $code,
        string $message,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        return $this->encryptCodableResponse($status, new Error($code, $message), $publicKey);
    }

    /**
     * Decrypt response, mainly used for testing.
     *
     * @throws Exception
     */
    public function decryptResponse(
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
     * Decrypt response, mainly used for testing.
     *
     * @template T of Codable
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function decryptCodableResponse(
        EncryptedResponse $response,
        string $class,
        string $keyPair
    ): Codable {
        $json = $this->decryptResponse($response, $keyPair);
        $decoder = new JSONDecoder();
        return $decoder->decode($json)->decodeObject($class);
    }
}
