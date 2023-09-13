<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class ApplicationFile implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly string $id,
        public readonly string $fieldCode,
        public readonly string $originalName,
        public readonly string $mimeType,
        public readonly int $size
    ) {
    }
}
