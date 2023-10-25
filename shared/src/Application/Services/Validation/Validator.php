<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\InvokableValidationRule;
use Illuminate\Validation\Validator as BaseValidator;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ErrorMessageResultRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ImplicitValidationRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\SuccessMessageResultRule;

class Validator extends BaseValidator
{
    /**
     * @var array<string, array<string>>
     */
    public array $successMessages = [];

    /**
     * @var array<string, array<string>>
     */
    public array $errorMessages = [];

    public function __construct(
        Translator $translator,
        array $data,
        array $rules,
        private readonly ValidatorServicesContainer $servicesContainer
    ) {
        parent::__construct($translator, $data, $rules);
    }

    private function collectErrorAndSuccessMessages(mixed $invokableRule, string $attribute): void
    {
        if ($invokableRule instanceof SuccessMessageResultRule) {
            $successMessages = $invokableRule->getSuccessMessages();
            if (!empty($successMessages)) {
                $this->successMessages[$attribute] = $successMessages;
            }
        }

        if ($invokableRule instanceof ErrorMessageResultRule) {
            $errorMessages = $invokableRule->getErrorMessages();
            if (!empty($errorMessages)) {
                $this->errorMessages[$attribute] = $errorMessages;
            }
        }
    }

    protected function isImplicit($rule)
    {
        if (parent::isImplicit($rule)) {
            return true;
        }

        if ($rule instanceof InvokableValidationRule) {
            $rule = $rule->invokable();
        }

        return $rule instanceof ImplicitValidationRule;
    }

    /**
     * Validate an attribute using a custom rule object.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Rule  $rule
     * @return void
     */
    protected function validateUsingCustomRule($attribute, $value, $rule): void
    {
        if (!($rule instanceof InvokableValidationRule)) {
            parent::validateUsingCustomRule($attribute, $value, $rule);
            return;
        }

        $invokableRule = $rule->invokable();

        // Access services from container
        $services = $this->servicesContainer;

        if ($invokableRule instanceof FieldValuesAwareRule) {
            $invokableRule->setFieldValues($services->getFieldValues());
        }

        if ($invokableRule instanceof ApplicationStageAwareRule) {
            $invokableRule->setApplicationStage($services->getApplicationStage());
        }

        if ($invokableRule instanceof ApplicationFileManagerAwareRule) {
            $invokableRule->setApplicationFileManager($services->getApplicationFileManager());
        }

        if ($invokableRule instanceof ApplicationRepositoryAwareRule) {
            $invokableRule->setApplicationRepository($services->getApplicationRepository());
        }

        parent::validateUsingCustomRule($attribute, $value, $rule);

        $this->collectErrorAndSuccessMessages($invokableRule, $attribute);
    }
}
