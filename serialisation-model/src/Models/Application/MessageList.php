<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class MessageList implements Codable
{
    /**
     * @param array<MessageListMessage> $messages
     */
    final public function __construct(public readonly array $messages)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $messages = $container->{'messages'}->decodeArray(MessageListMessage::class);
        return new static($messages);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'messages'} = $this->messages;
    }
}
