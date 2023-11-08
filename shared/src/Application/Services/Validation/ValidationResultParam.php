<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class ValidationResultParam implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly string $code,
        public readonly string $value,
    ) {
    }
}
