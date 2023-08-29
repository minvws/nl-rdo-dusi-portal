<?php
declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class MessageListMessage implements Codable
{
    final public function __construct(
        public readonly string $id,
        public readonly string $subject,
        public readonly \DateTimeInterface $sentAt,
        public readonly bool $isNew
    ) {
    }

    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $id = $container->{'id'}->decodeString();
        $subject = $container->{'subject'}->decodeString();
        $sentAt = $container->{'sentAt'}->decodeDateTime();
        $isNew = $container->{'isNew'}->decodeBool();
        return new self($id, $subject, $sentAt, $isNew);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'id'} = $this->id;
        $container->{'subject'} = $this->subject;
        $container->{'sentAt'} = $this->sentAt;
        $container->{'isNew'} = $this->isNew;
    }
}