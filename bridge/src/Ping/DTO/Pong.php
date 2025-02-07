<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Ping\DTO;

use DateTimeImmutable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\Codable\Reflection\Attributes\CodableDateTime;

readonly class Pong implements Codable
{
    use CodableSupport;

    public const DATETIME_FORMAT = Ping::DATETIME_FORMAT;

    public function __construct(
        #[CodableDateTime(format: self::DATETIME_FORMAT)] public DateTimeImmutable $requestStamp,
        #[CodableDateTime(format: self::DATETIME_FORMAT)] public DateTimeImmutable $responseStamp
    ) {
    }
}
