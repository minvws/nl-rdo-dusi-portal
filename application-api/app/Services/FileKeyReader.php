<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Interfaces\KeyReader;
use MinVWS\DUSi\Application\API\Services\Exceptions\DataEncryptionException;

class FileKeyReader implements KeyReader
{
    public function __construct(private readonly string $publicKeyPath)
    {
    }

    protected string $cert;

    /**
     * Lazy loads the key from the file if not already loaded.
     *
     * @throws DataEncryptionException
     */
    protected function lazyLoadKey(): void
    {
        $cert = file_get_contents($this->publicKeyPath);
        if (empty($cert)) {
            throw new DataEncryptionException("Could not read public key");
        }

        $this->cert = $cert;
    }

    /**
     * @throws DataEncryptionException
     */
    public function getKey(): string
    {
        if (!isset($this->cert)) {
            $this->lazyLoadKey();
        }
        return $this->cert;
    }
}
