<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Interfaces\KeyReader;
use RuntimeException;

class FileKeyReader implements KeyReader
{
    public function __construct(private readonly string $publicKeyPath)
    {
    }

    protected string $cert;

    /**
     * Lazy loads the key from the file if not already loaded.
     *
     * @throws RuntimeException
     */
    protected function lazyLoadKey(): void
    {
        $cert = file_get_contents($this->publicKeyPath);
        if (empty($cert)) {
            throw new RuntimeException("Could not read public key");
        }

        $this->cert = $cert;
    }

    /**
     * @throws RuntimeException
     */
    public function getKey(): string
    {
        if (!isset($this->cert)) {
            $this->lazyLoadKey();
        }
        return $this->cert;
    }
}
