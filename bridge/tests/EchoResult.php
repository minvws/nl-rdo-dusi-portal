<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Tests;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class EchoResult implements Codable
{
    public function __construct(public readonly string $message)
    {
    }

    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        return new self($container->decodeString());
    }

    public function encode(EncodingContainer $container): void
    {
        $container->encodeString($this->message);
    }
}
