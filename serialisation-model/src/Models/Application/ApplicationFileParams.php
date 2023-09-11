<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class ApplicationFileParams implements Codable
{
    use CodableSupport;

    final public function __construct(
        public readonly EncryptedIdentity $identity,
        public readonly ClientPublicKey $publicKey,
        public readonly string $applicationReference,
        public readonly string $fieldCode,
        public readonly string $id
    ) {
    }
}
