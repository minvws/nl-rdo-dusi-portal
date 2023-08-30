<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class ApplicationListParams implements Codable
{
    final public function __construct(
        public readonly Identity $identity,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     * @psalm-suppress NoValue
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $identity = $container->{'identity'}->decodeObject(Identity::class);
        return new static($identity);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'identity'} = $this->identity;
    }
}
