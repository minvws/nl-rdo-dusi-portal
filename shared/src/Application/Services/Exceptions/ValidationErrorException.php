<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Exceptions;

use Exception;

class ValidationErrorException extends Exception
{
    /**
     * @param array $validationResults
     */
    public function __construct(private readonly array $validationResults)
    {
        parent::__construct("Validation error in validation result!", 422);
    }

    public function getValidationResults(): array
    {
        return $this->validationResults;
    }
}
