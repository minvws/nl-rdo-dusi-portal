<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\ReviewDeadlineSource;

readonly class AssignationDeadlineFieldParams implements Codable
{
    use CodableSupport;

    public function __construct(
        public ?ReviewDeadlineSource $deadlineSource = null,
        public ?FieldReference $deadlineSourceFieldReference = null,
        public ?string $deadlineAdditionalPeriod = null,
        public ?FieldReference $deadlineOverrideFieldReference = null,
    ) {
    }
}
