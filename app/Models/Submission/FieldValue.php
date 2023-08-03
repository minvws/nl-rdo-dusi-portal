<?php

declare(strict_types=1);

namespace App\Models\Submission;

use MinVWS\DUSi\Shared\Subsidy\Models\Field;

readonly class FieldValue
{
    public function __construct(
        public Field $field,
        public string|int|bool|float|null $value
    ) {
    }
}
