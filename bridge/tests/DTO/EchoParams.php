<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Tests\DTO;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class EchoParams implements Codable
{
    public function __construct(public readonly string $message, public readonly int $times)
    {
    }

    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        return new self($container->{'message'}->decodeString(), $container->{'times'}->decodeInt());
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'message'} = $this->message;
        $container->{'times'} = $this->times;
    }
}
