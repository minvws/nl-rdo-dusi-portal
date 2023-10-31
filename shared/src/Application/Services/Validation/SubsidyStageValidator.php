<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Validation\ValidationException;

class SubsidyStageValidator
{
    public function __construct(private readonly CustomRuleValidator $customRuleValidator)
    {
    }

    public function validate(): array
    {
        try {
            $this->customRuleValidator->validate();
            return $this->customRuleValidator->validationResults->toArray();
        } catch (ValidationException $e) {
            $validationResults = collect($e->errors())->map(function (array $errors) {
                  return array_map(fn($error) => ValidationResultFactory::createError($error), $errors);
            });
            return array_merge($validationResults->toArray(), $this->customRuleValidator->validationResults->toArray());
        }
    }
}
