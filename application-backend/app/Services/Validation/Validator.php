<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\InvokableValidationRule;
use Illuminate\Validation\Validator as BaseValidator;
use MinVWS\DUSi\Application\Backend\Services\ApplicationFileService;
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
    protected ApplicationFileService $applicationFileService;
    protected ApplicationRepository $applicationRepository;

    /**
     * Create a new Validator instance.
     *
     * @param Translator $translator
     * @param array $data
     * @param array $rules
     * @param ApplicationStageVersion $applicationStageVersion
     * @param array<string, FieldValue> $fieldValues
     * @param ApplicationFileService $applicationFileService
     * @param ApplicationRepository $applicationRepository
     */
    public function __construct(
        Translator $translator,
        array $data,
        array $rules,
        ApplicationStageVersion $applicationStageVersion,
        array $fieldValues,
        ApplicationFileService $applicationFileService,
        ApplicationRepository $applicationRepository,
    ) {
        parent::__construct($translator, $data, $rules);

        $this->fieldValues = $fieldValues;
        $this->applicationStageVersion = $applicationStageVersion;
        $this->applicationFileService = $applicationFileService;
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
            $invokableRule->setFieldValues($this->data);
        }

        if ($invokableRule instanceof ApplicationStageVersionAwareRule) {
            $invokableRule->setApplicationStageVersion($this->applicationStageVersion);
        }

        if ($invokableRule instanceof ApplicationFileServiceAwareRule) {
            $invokableRule->setApplicationFileService($this->applicationFileService);
        }

        if ($invokableRule instanceof ApplicationRepositoryAwareRule) {
            $invokableRule->setApplicationRepository($this->applicationRepository);
        }

        parent::validateUsingCustomRule($attribute, $value, $rule);
    }
}
