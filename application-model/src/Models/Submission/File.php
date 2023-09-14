<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Submission;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class File implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly string $id,
        public readonly ?string $name,
        public readonly ?string $mimeType
    ) {
    }
}