<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\DUSi\Shared\Application\Services\Validation\Enums\ValidationResultType;

class ValidationResult implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly ValidationResultType $type,
        public readonly string $message,
        public ?array $params = [],
    ) {
    }

    public function setParam(string $key, mixed $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }
}
