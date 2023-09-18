<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

readonly class EncryptedIdentity implements Codable
{
    use CodableSupport;

    public function __construct(
        public IdentityType $type,
        public HsmEncryptedData $encryptedIdentifier,
    ) {
    }
}
