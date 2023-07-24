<?php
declare(strict_types=1);

namespace App\Shared\Models\Definition\Submission;

use App\Shared\Models\Definition\Field;

readonly class FieldValue
{
    public function __construct(
        public Field $field,
        public string|int|bool|float|null $value
    ) {
    }
}
