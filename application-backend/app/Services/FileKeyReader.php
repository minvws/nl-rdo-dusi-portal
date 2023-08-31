<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Config;
use MinVWS\DUSi\Application\Backend\Interfaces\KeyReader;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\EncryptionException;

class FileKeyReader implements KeyReader
{
    protected string $cert;

    /**
     * Lazy loads the key from the file if not already loaded.
     *
     * @throws EncryptionException
     */
    protected function lazyLoadKey(): void
    {
        $cert = file_get_contents(Config::get('encryption.public_key'));

        if (empty($cert)) {
            throw new EncryptionException("Could not read public key");
        }

        $this->cert = $cert;
    }

    /**
     * @throws EncryptionException
     */
    public function getKey(): string
    {
        if (!isset($this->cert)) {
            $this->lazyLoadKey();
        }
        return $this->cert;
    }
}
