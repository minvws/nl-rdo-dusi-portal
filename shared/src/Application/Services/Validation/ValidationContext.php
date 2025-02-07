<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

class ValidationContext
{
    public function __construct(
        private ApplicationStage $applicationStage,
        private array $fieldValues,
    ) {
    }

    public function getApplicationStage(): ApplicationStage
    {
        return $this->applicationStage;
    }

    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }
}
