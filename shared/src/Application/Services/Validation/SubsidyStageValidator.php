<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ValidationErrorException;
use MinVWS\DUSi\Shared\Application\Services\Validation\Enums\ValidationResultType;

class SubsidyStageValidator
{
    private array $validationResults;

    public function __construct(private readonly CustomRuleValidator $customRuleValidator)
    {
    }

    /**
     * @throws ValidationErrorException
     */
    public function validate(): array
    {
        try {
            $this->customRuleValidator->validate();
            $this->validationResults = $this->customRuleValidator->validationResults->toArray();
        } catch (ValidationException $e) {
            $this->handleValidationException($e);
        }

        if ($this->fails()) {
            throw new ValidationErrorException($this->validationResults);
        }

        return $this->validationResults;
    }

    private function handleValidationException(ValidationException $e): void
    {
        $validationResults = collect($e->errors())->map(function (array $errors) {
            return array_map(fn($error) => ValidationResultFactory::createError($error), $errors);
        });

        $this->validationResults = array_merge(
            $validationResults->toArray(),
            $this->customRuleValidator->validationResults->toArray()
        );
    }

    public function fails(): bool
    {
        if (!isset($this->validationResults)) {
            $this->validate();
        }

        foreach ($this->validationResults as $validationResultsForField) {
            if ($this->hasErrors($validationResultsForField)) {
                return true;
            }
        }

        return false;
    }

    private function hasErrors(array $validationResults): bool
    {
        foreach ($validationResults as $validationResult) {
            if ($validationResult->type === ValidationResultType::Error) {
                return true;
            }
        }

        return false;
    }
}
