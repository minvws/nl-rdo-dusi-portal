<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class ApplicationList implements Codable
{
    /**
     * @param array<ApplicationListApplication> $items
     */
    final public function __construct(public readonly array $items)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $items = $container->{'items'}->decodeArray(ApplicationListApplication::class);
        return new static($items);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'items'} = $this->items;
    }
}
