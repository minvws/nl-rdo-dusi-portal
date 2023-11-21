<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidationResult;

interface ValidationResultRule
{
    /**
     * @returns Collection<ValidationResult>
     */
    public function getValidationResults(): Collection;
}
