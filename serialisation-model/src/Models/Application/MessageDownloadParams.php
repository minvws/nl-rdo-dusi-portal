<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class MessageDownloadParams implements Codable
{
    use CodableSupport;

    final public function __construct(
        public readonly Identity $identity,
        public readonly string $publicKey,
        public readonly string $id,
        public readonly MessageDownloadFormat $format
    ) {
    }
}
