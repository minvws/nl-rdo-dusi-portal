<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Submission;

use MinVWS\DUSi\Shared\Application\Shared\Models\Definition\Field;

readonly class FieldValue
{
    public function __construct(
        public Field $field,
        public string|int|bool|float|null $value
    ) {
    }
}
