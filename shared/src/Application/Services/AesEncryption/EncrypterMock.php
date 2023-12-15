<?php

namespace MinVWS\DUSi\Shared\Application\Services\AesEncryption;

use Illuminate\Contracts\Encryption\Encrypter;

class EncrypterMock implements Encrypter {

    public function __construct(private string $key)
    {
    }

    public function encrypt($value, $serialize = true)
    {
        return $this->key . $value;
    }

    public function decrypt($payload, $unserialize = true)
    {
        if (substr($payload, 0, strlen($this->key)) !== $this->key) {
            throw new \Exception('Encrypted with different key');
        }
        return substr($payload, strlen($this->key));
    }

    public function getKey()
    {
        return $this->key;
    }
}
