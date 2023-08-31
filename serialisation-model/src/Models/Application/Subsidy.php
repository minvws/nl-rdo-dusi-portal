<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class Subsidy implements Codable
{
    final public function __construct(
        public readonly string $id,
        public readonly string $title
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     * @psalm-suppress NoValue
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $id = $container->{'id'}->decodeString();
        $title = $container->{'title'}->decodeString();
        return new static($id, $title);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'id'} = $this->id;
        $container->{'title'} = $this->title;
    }
}
