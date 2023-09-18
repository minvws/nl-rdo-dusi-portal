<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class ActionableCounts implements Codable
{
    final public function __construct(
        public int $messageCount,
        public int $applicationCount
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $messageCount = $container->{'messageCount'}->decodeInt();
        $applicationCount = $container->{'applicationCount'}->decodeInt();
        return new static($messageCount, $applicationCount);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'messageCount'} = $this->messageCount;
        $container->{'applicationCount'} = $this->applicationCount;
    }
}
