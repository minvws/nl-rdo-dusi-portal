<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Illuminate\Support\Collection;

interface ValidationResultRule
{
    /**
     * @returns Collection<ValidationResult>
     */
    public function getValidationResults(): Collection;
}
