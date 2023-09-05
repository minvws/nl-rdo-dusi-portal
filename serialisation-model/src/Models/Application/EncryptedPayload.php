<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class EncryptedPayload implements Codable
{
    /**
     * @param string $data Encrypted data (binary).
     */
    final public function __construct(public readonly string $data)
    {
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $data = base64_decode($container->decodeString());
        return new static($data);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->encodeString(base64_encode($this->data));
    }
}
