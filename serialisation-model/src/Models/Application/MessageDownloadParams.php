<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\Exceptions\CodablePathException;

class MessageDownloadParams implements Codable
{
    final public function __construct(
        public readonly Identity $identity,
        public readonly string $publicKey,
        public readonly string $id,
        public readonly MessageDownloadFormat $format
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $identity = $container->{'identity'}->decodeObject(Identity::class);
        $publicKey = base64_decode($container->{'publicKey'}->decodeString());
        $id = $container->{'id'}->decodeString();
        $format = MessageDownloadFormat::tryFrom($container->{'format'}->decodeString());
        if ($format === null) {
            throw new CodablePathException(
                $container->getPath(),
                "Invalid format at path " . CodablePathException::convertPathToString($container->getPath())
            );
        }
        return new static($identity, $publicKey, $id, $format);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'identity'} = $this->identity;
        $container->{'publicKey'} = base64_encode($this->publicKey);
        $container->{'id'} = $this->id;
        $container->{'format'} = $this->format->value;
    }
}
