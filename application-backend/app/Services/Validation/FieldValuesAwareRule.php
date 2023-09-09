<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

interface FieldValuesAwareRule
{
    /**
     * Set the field values under validation.
     * @param array<int|string, FieldValue> $fieldValues
     * @return void
     */
    public function setFieldValues(array $fieldValues): void;
}
