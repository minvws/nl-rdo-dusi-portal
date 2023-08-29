<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Ping\DTO;

use DateTimeImmutable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class Pong implements Codable
{
    public const DATETIME_FORMAT = Ping::DATETIME_FORMAT;
    final public function __construct(
        public readonly DateTimeImmutable $requestStamp,
        public readonly DateTimeImmutable $responseStamp
    ) {
    }

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $requestStamp = $container->{'requestStamp'}->decodeDateTime(self::DATETIME_FORMAT);
        $responseStamp = $container->{'responseStamp'}->decodeDateTime(self::DATETIME_FORMAT);
        return new static($requestStamp, $responseStamp);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'requestStamp'}->encodeDateTime($this->requestStamp, self::DATETIME_FORMAT);
        $container->{'responseStamp'}->encodeDateTime($this->responseStamp, self::DATETIME_FORMAT);
    }
}
