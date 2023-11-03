<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Validation\InvokableValidationRule;
use Illuminate\Validation\Validator as BaseValidator;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ValidationResultRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ImplicitValidationRule;

class CustomRuleValidator extends BaseValidator
{
    /**
     * @var Collection<string, array>
     */
    public Collection $validationResults;

    public function __construct(
        Translator $translator,
        array $data,
        array $rules,
        private readonly ValidatorServicesContainer $servicesContainer
    ) {
        parent::__construct($translator, $data, $rules);

        $this->validationResults = collect();
    }

    private function collectRuleValidationResults(mixed $invokableRule, string $attribute): void
    {
        if ($invokableRule instanceof ValidationResultRule) {
            $ruleValidationResults = $invokableRule->getValidationResults();
            if (!$ruleValidationResults->isEmpty()) {
                $this->validationResults->put($attribute, $ruleValidationResults->toArray());
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

        $this->collectRuleValidationResults($invokableRule, $attribute);
    }
}
