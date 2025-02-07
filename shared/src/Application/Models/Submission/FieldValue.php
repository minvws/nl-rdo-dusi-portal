<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Submission;

use MinVWS\DUSi\Shared\Subsidy\Models\Field;

readonly class FieldValue
{
    /**
     * @param Field $field
     * @param FileList|string|int|bool|float|array<mixed>|null $value
     */
    public function __construct(
        public Field $field,
        public FileList|string|int|bool|float|array|null $value
    ) {
    }

    public function valueToString(): string
    {
        if (is_array($this->value)) {
            return implode(', ', $this->value);
        }

        if ($this->value instanceof FileList) {
            // Assuming FileList has a __toString() method
            return $this->value->__toString();
        }

        if (is_null($this->value)) {
            return '';
        }

        return (string) $this->value;
    }
}
