<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

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
