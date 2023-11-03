<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\DUSi\Shared\Application\Services\Validation\Enums\ValidationResultType;

class ValidationResultFactory
{
    public static function createError(string $message): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Error,
            message: $message,
        );
    }

    public static function createConfirmation(string $message): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Success,
            message: $message,
        );
    }

    public static function createExplanation(string $message): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Explanation,
            message: $message,
        );
    }

    public static function createWarning(string $message): ValidationResult
    {
        return new ValidationResult(
            type: ValidationResultType::Warning,
            message: $message,
        );
    }
}
