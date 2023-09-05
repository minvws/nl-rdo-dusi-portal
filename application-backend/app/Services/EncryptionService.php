<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Config;
use Exception;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Interfaces\KeyReader;
use MinVWS\DUSi\Application\Backend\Services\Hsm\HsmService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity;

class EncryptionService
{
    private \OpenSSLAsymmetricKey $publicKey;
    private string $aesKey;
    private string $initializationVector;

    public function __construct(protected KeyReader $keyReader, protected HsmService $hsmService)
    {
    }

    /**
     * @throws Exception
     */
    public function decryptFormSubmit(FormSubmit $encryptedForm): FormSubmit
    {
        $identifier = $this->decryptData($encryptedForm->identity->identifier);

        return new FormSubmit(
            identity: new Identity($encryptedForm->identity->type, $identifier),
            applicationMetadata: $encryptedForm->applicationMetadata,
            encryptedData: $this->decryptData($encryptedForm->encryptedData),
        );
    }

    /**
     * @throws Exception
     */
    public function decryptFileUpload(FileUpload $encryptedData): FileUpload
    {
        $identifier = $this->decryptData($encryptedData->identity->identifier);

        return new FileUpload(
            identity: new Identity($encryptedData->identity->type, $identifier),
            applicationMetadata: $encryptedData->applicationMetadata,
            fieldCode: $encryptedData->fieldCode,
            id: $encryptedData->id,
            mimeType: $encryptedData->mimeType,
            extension: $encryptedData->extension,
            encryptedContents: $this->decryptData($encryptedData->encryptedContents),
        );
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
     * @param string $encryptedData
     * @return string
     * @throws Exception
     */
    protected function decryptData(string $encryptedData): string
    {
        $encryptedData = base64_decode($encryptedData);
        $dataArray = json_decode($encryptedData, true);

        $aesKeyDecrypted = $this->decryptAesKey($dataArray['encrypted_aes']);

        return $this->decryptAesEncrypted($dataArray['encrypted'], $aesKeyDecrypted, $dataArray['iv']);
    }

    public function encryptResponse(
        EncryptedResponseStatus $status,
        Codable $payload,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $encoder = new JSONEncoder();
        $json = $encoder->encode($payload);
        openssl_public_encrypt($json, $data, $publicKey->value, OPENSSL_PKCS1_OAEP_PADDING);
        return new EncryptedResponse($status, $data);
    }
}
