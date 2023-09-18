<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class ClientPublicKey implements Codable
{
    public function __construct(public readonly string $value)
    {
    }

    public function encode(EncodingContainer $container): void
    {
        $container->encodeString(base64_encode($this->value));
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?object $object = null): self
    {
        $result = base64_decode($container->decodeString());
        assert(is_string($result)); // should always work
        return new self($result);
    }
}
