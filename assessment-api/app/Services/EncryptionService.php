<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Config;
use Exception;
use MinVWS\DUSi\Assessment\API\Services\Hsm\HsmService;
use SodiumException;

class EncryptionService
{
    public function __construct(protected HsmService $hsmService)
    {
    }

    /**
     * @throws SodiumException
     */
    public function sodiumEncrypt(string $data, string $publicKey): string
    {
        return base64_encode(sodium_crypto_box_seal($data, $publicKey));
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
    public function decryptData(string $encryptedData): string
    {
        $encryptedData = base64_decode($encryptedData);
        $dataArray = json_decode($encryptedData, true);

        $aesKeyDecrypted = $this->decryptAesKey($dataArray['encrypted_aes']);

        return $this->decryptAesEncrypted($dataArray['encrypted'], $aesKeyDecrypted, $dataArray['iv']);
    }
}
