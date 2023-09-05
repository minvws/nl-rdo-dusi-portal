<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\Codable\Reflection\Attributes\CodableArray;

class MessageList implements Codable
{
    use CodableSupport;

    /**
     * @param array<MessageListMessage> $items
     */
    final public function __construct(#[CodableArray(MessageListMessage::class)] public readonly array $items)
    {
    }
}
