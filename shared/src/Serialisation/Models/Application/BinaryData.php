<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class BinaryData implements Codable
{
    public function __construct(public readonly string $data)
    {
    }

    public static function forBase64EncodedData(string $data): BinaryData
    {
        return new self(base64_decode($data));
    }

    public function getBase64EncodedData(): string
    {
        return base64_encode($this->data);
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): self
    {
        return self::forBase64EncodedData($container->decodeString());
    }

    public function encode(EncodingContainer $container): void
    {
        $container->encodeString($this->getBase64EncodedData());
    }
}
