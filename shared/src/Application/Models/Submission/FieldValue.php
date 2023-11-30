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

    public function valueToString(): string
    {
        if (is_array($this->value)) {
            return implode(', ', $this->value);
        } elseif ($this->value instanceof FileList) {
            // Assuming FileList has a __toString() method
            return $this->value->__toString();
        } elseif (is_null($this->value)) {
            return '';
        } else {
            return (string) $this->value;
        }
    }
}
