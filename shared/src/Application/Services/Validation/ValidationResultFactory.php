<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\DUSi\Shared\Application\Services\Validation\Enums\ValidationResultType;

class ValidationResultFactory
{
    public static function createError(string $message, ?string $id = null): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Error,
            message: $message,
            id: $id,
        );
    }

    public static function createConfirmation(string $message, ?string $id = null): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Success,
            message: $message,
            id: $id,
        );
    }

    public static function createExplanation(string $message, ?string $id = null): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Explanation,
            message: $message,
            id: $id,
        );
    }

    public static function createWarning(string $message, ?string $id = null): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Warning,
            message: $message,
            id: $id,
        );
    }
}
