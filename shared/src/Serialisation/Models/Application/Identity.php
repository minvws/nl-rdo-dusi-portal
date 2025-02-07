<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

readonly class Identity implements Codable
{
    use CodableSupport;

    public function __construct(
        public IdentityType $type,
        public string $identifier
    ) {
    }
}
