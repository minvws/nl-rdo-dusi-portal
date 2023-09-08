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

    /**
     * @throws Exception
     */
    public function encryptResponse(
        EncryptedResponseStatus $status,
        ?Codable $payload,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $encoder = new JSONEncoder();
        $json = $encoder->encode($payload);

        $key = random_bytes(32);
        if (!openssl_public_encrypt($key, $encryptedKey, $publicKey->value, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new Exception('Encryption of key failed: ' . openssl_error_string());
        }

        $initializationVector = random_bytes(16);

        $data = openssl_encrypt($json, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $initializationVector);
        if ($data === false) {
            throw new Exception('Encryption of data failed: ' . openssl_error_string());
        }

        return new EncryptedResponse($status, $encryptedKey, $initializationVector, $data);
    }

    /**
     * @template T of Codable
     *
     * @param class-string<T> $expectedClass
     *
     * @return T|null
     */
    public function decryptResponse(
        EncryptedResponse $response,
        OpenSSLAsymmetricKey $privateKey,
        string $expectedClass
    ): ?Codable {
        if (!openssl_private_decrypt($response->key, $key, $privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new Exception('Decryption of key failed: ' . openssl_error_string());
        }

        $json = openssl_decrypt(
            $response->data,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $response->initializationVector
        );

        if ($json === false) {
            throw new Exception('Decryption of data failed: ' . openssl_error_string());
        }

        $decoder = new JSONDecoder();
        return $decoder->decode($json)->decodeObjectIfPresent($expectedClass);
    }
}
