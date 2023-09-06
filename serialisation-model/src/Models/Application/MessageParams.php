<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class MessageParams implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly Identity $identity,
        public readonly ClientPublicKey $publicKey,
        public readonly string $id
    ) {
    }
}
