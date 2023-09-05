<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class MessageDownload implements Codable
{
    public function __construct(
        public readonly string $contentType,
        public readonly string $data
    ) {
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'contentType'} = $this->contentType;
        $container->{'data'} = base64_encode($this->data);
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): Decodable
    {
        $contentType = $container->{'contentType'}->decodeString();
        $data = base64_decode($container->{'data'}->decodeString());
        return new self($contentType, $data);
    }
}
