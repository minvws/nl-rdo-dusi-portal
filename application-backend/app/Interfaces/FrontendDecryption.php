<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Interfaces;

use MinVWS\Codable\Coding\Codable;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;

interface FrontendDecryption
{
    /**
     * @param string $encryptedData Base64 encoded encrypted data
     * @return string Decrypted data
     * @throws FrontendDecryptionFailedException
     */
    public function decrypt(string $encryptedData): string;

    /**
     * @param class-string<T> $class
     * @return T
     * @template T of Codable
     * @throws FrontendDecryptionFailedException
     */
    public function decryptCodable(
        BinaryData|string $data,
        string $class
    ): Codable;
}
