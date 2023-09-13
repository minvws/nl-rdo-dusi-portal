<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Interfaces;

use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;

interface FrontendDecryption
{
    /**
     * @param string $encryptedData Base64 encoded encrypted data
     * @return string Decrypted data
     * @throws FrontendDecryptionFailedException
     */
    public function decrypt(string $encryptedData): string;
}
