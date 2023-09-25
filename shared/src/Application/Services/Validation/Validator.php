<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\InvokableValidationRule;
use Illuminate\Validation\Validator as BaseValidator;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ImplicitValidationRule;

class Validator extends BaseValidator
{
    /**
     * @param array<int|string, FieldValue> $fieldValues
     */
    public function __construct(
        Translator $translator,
        array $data,
        array $rules,
        private readonly ApplicationStage $applicationStage,
        private readonly array $fieldValues,
        private readonly ApplicationFileManager $applicationFileManager,
        private readonly ApplicationRepository $applicationRepository
    ) {
        parent::__construct($translator, $data, $rules);
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
            $invokableRule->setFieldValues($this->fieldValues);
        }

        if ($invokableRule instanceof ApplicationStageAwareRule) {
            $invokableRule->setApplicationStage($this->applicationStage);
        }

        if ($invokableRule instanceof ApplicationFileManagerAwareRule) {
            $invokableRule->setApplicationFileManager($this->applicationFileManager);
        }

        if ($invokableRule instanceof ApplicationRepositoryAwareRule) {
            $invokableRule->setApplicationRepository($this->applicationRepository);
        }

        parent::validateUsingCustomRule($attribute, $value, $rule);
    }
}
