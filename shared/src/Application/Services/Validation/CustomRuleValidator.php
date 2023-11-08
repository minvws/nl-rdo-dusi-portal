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
        private readonly ValidationContext $validationContext,
        private readonly ValidationServiceContainer $serviceContainer
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

        if ($invokableRule instanceof FieldValuesAwareRule) {
            $invokableRule->setFieldValues($this->validationContext->getFieldValues());
        }

        if ($invokableRule instanceof ApplicationStageAwareRule) {
            $invokableRule->setApplicationStage($this->validationContext->getApplicationStage());
        }

        if ($invokableRule instanceof ApplicationFileManagerAwareRule) {
            $invokableRule->setApplicationFileManager($this->serviceContainer->getApplicationFileManager());
        }

        if ($invokableRule instanceof ApplicationRepositoryAwareRule) {
            $invokableRule->setApplicationRepository($this->serviceContainer->getApplicationRepository());
        }

        parent::validateUsingCustomRule($attribute, $value, $rule);

        $this->collectRuleValidationResults($invokableRule, $attribute);
    }
}
