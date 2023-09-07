<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

readonly class EncryptedIdentity implements Codable
{
    public function __construct(
        public IdentityType $type,
        public string $encryptedIdentifier
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?object $object = null): self
    {
        $type = $container->{'type'}->decodeEnum(IdentityType::class);
        $encryptedIdentifier = base64_decode($container->{'encryptedIdentifier'}->decodeString());
        return new self($type, $encryptedIdentifier);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'type'} = $this->type;
        $container->{'encryptedIdentifier'} = base64_encode($this->encryptedIdentifier);
    }
}
