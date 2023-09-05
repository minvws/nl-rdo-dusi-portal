<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class Message implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly string $id,
        public readonly string $subject,
        public readonly DateTimeInterface $sentAt,
        public readonly bool $isNew,
        public readonly string $body
    ) {
    }
}
