<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\InvokableValidationRule;
use Illuminate\Validation\Validator as BaseValidator;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;

class Validator extends BaseValidator
{
    /**
     * @var array<string, FieldValue> $fieldValues
     */
    protected array $fieldValues;

    protected ApplicationStageVersion $applicationStageVersion;
    protected ApplicationFileRepository $applicationFileRepository;
    protected ApplicationRepository $applicationRepository;

    /**
     * Create a new Validator instance.
     *
     * @param Translator $translator
     * @param array $data
     * @param array $rules
     * @param ApplicationStageVersion $applicationStageVersion
     * @param array<string, FieldValue> $fieldValues
     * @param ApplicationFileRepository $applicationFileRepository
     * @param ApplicationRepository $applicationRepository
     */
    public function __construct(
        Translator $translator,
        array $data,
        array $rules,
        ApplicationStageVersion $applicationStageVersion,
        array $fieldValues,
        ApplicationFileRepository $applicationFileRepository,
        ApplicationRepository $applicationRepository,
    ) {
        parent::__construct($translator, $data, $rules);

        $this->fieldValues = $fieldValues;
        $this->applicationStageVersion = $applicationStageVersion;
        $this->applicationFileRepository = $applicationFileRepository;
        $this->applicationRepository = $applicationRepository;
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

        if ($invokableRule instanceof ApplicationStageVersionAwareRule) {
            $invokableRule->setApplicationStageVersion($this->applicationStageVersion);
        }

        if ($invokableRule instanceof ApplicationFileRepositoryAwareRule) {
            $invokableRule->setApplicationFileRepository($this->applicationFileRepository);
        }

        if ($invokableRule instanceof ApplicationRepositoryAwareRule) {
            $invokableRule->setApplicationRepository($this->applicationRepository);
        }

        parent::validateUsingCustomRule($attribute, $value, $rule);
    }
}
