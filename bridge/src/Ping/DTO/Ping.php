<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Ping\DTO;

use DateTimeImmutable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class Ping implements Codable
{
    public const DATETIME_FORMAT = 'Y-m-d\TH:i:s.up'; // includes microseconds

    final public function __construct(public readonly DateTimeImmutable $requestStamp)
    {
    }

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $stamp = $container->nestedContainer('requestStamp')->decodeDateTime(self::DATETIME_FORMAT);
        return new static($stamp);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'requestStamp'}->encodeDateTime($this->requestStamp, self::DATETIME_FORMAT);
    }
}
