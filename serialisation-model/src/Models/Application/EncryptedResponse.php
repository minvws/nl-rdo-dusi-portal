<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class EncryptedResponse implements Codable
{
    /**
     * @param string $data Encrypted data (binary).
     */
    public function __construct(
        public readonly EncryptedResponseStatus $status,
        public readonly string $contentType,
        public readonly string $data
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?object $object = null): self
    {
        $status = $container->{'status'}->decodeEnum(EncryptedResponseStatus::class);
        $contentType = $container->{'contentType'}->decodeString();
        $encodedData = $container->{'data'}->decodeString();
        $data = base64_decode($encodedData);
        return new self($status, $contentType, $data);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'status'} = $this->status;
        $container->{'contentType'} = $this->contentType;
        $container->{'data'} = base64_encode($this->data);
    }
}
