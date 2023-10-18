<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class FieldValidationResponse implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly array $validationResult,
    ) {
    }
}
