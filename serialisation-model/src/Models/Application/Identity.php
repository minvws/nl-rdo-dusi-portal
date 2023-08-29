<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\Exceptions\CodablePathException;

readonly class Identity implements Codable
{
    final public function __construct(
        public IdentityType $type,
        public string $identifier
    ) {
    }

    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $type = IdentityType::tryFrom($container->{'type'}->decodeString());
        if ($type === null) {
            throw new CodablePathException($container->getPath(), "Invalid identity type at path " . CodablePathException::convertPathToString($container->getPath()));
        }

        $identifier = $container->{'identifier'}->decodeString();

        return new self($type, $identifier);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'type'} = $this->type->value;
        $container->{'identifier'} = $this->identifier;
    }
}
