<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Ping\DTO;

use DateTimeImmutable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\Codable\Reflection\Attributes\CodableDateTime;

readonly class Ping implements Codable
{
    use CodableSupport;

    public const DATETIME_FORMAT = 'Y-m-d\TH:i:s.up'; // includes microseconds

    final public function __construct(
        #[CodableDateTime(format: self::DATETIME_FORMAT)] public DateTimeImmutable $requestStamp
    ) {
    }
}
