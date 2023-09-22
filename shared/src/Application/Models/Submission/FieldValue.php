<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Submission;

use MinVWS\DUSi\Shared\Subsidy\Models\Field;

readonly class FieldValue
{
    public function __construct(
        public Field $field,
        public FileList|string|int|bool|float|array|null $value
    ) {
    }
}
