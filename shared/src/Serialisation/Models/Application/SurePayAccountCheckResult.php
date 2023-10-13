<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;

class SurePayAccountCheckResult implements Codable
{
    use CodableSupport;

    final public function __construct(
        public readonly NameMatchResult $nameMatchResult,
        public readonly ?string $nameSuggestion,
    ) {
    }
}
