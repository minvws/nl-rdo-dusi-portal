<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\AesEncryption;

use Illuminate\Contracts\Encryption\Encrypter;

class EncrypterMock implements Encrypter
{
    public function __construct(private string $key)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function encrypt($value, $serialize = true)
    {
        return $this->key . $serialize ? serialize($value) : $value;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decrypt($payload, $unserialize = true)
    {
        if (substr($payload, 0, strlen($this->key)) !== $this->key) {
            throw new \Exception('Encrypted with different key');
        }
        $decrypted = substr($payload, strlen($this->key))
        return $unserialize ? unserialize($decrypted) : $decrypted
    }

    public function getKey()
    {
        return $this->key;
    }
}
