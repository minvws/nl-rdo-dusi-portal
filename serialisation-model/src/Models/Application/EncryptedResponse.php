<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class EncryptedResponse implements Codable
{
    /**
     * @param string $data Encrypted data (binary).
     */
    public function __construct(
        public readonly EncryptedResponseStatus $status,
        public readonly string $key,
        public readonly string $initializationVector,
        public readonly string $data
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?object $object = null): self
    {
        $status = $container->{'status'}->decodeEnum(EncryptedResponseStatus::class);
        $encryptedKey = base64_decode($container->{'key'}->decodeString());
        $initializationVector = base64_decode($container->{'initializationVector'}->decodeString());
        $encryptedData = base64_decode($container->{'data'}->decodeString());
        return new self($status, $encryptedKey, $initializationVector, $encryptedData);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'status'} = $this->status;
        $container->{'key'} = base64_encode($this->key);
        $container->{'initializationVector'} = base64_encode($this->data);
        $container->{'data'} = base64_encode($this->data);
    }
}
