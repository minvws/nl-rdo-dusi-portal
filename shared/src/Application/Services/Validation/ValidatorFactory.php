<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Translation\Translator;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;

class ValidatorFactory
{
    public function __construct(
        private readonly ApplicationFileManager $applicationFileManager,
        private readonly ApplicationRepository $applicationRepository,
        private readonly Translator $translator,
    ) {
    }

    /**
     * @param ApplicationStage $applicationStage
     * @param array<int|string, FieldValue> $fieldValues
     * @param array<string, mixed> $data
     * @param array<string, mixed> $rules
     * @return CustomRuleValidator
     */
    public function getValidator(
        ApplicationStage $applicationStage,
        array $fieldValues,
        array $data,
        array $rules,
    ): CustomRuleValidator {
        $validationContext = new ValidationContext(
            applicationStage: $applicationStage,
            fieldValues: $fieldValues,
        );

        $serviceContainer = new ValidationServiceContainer(
            applicationFileManager: $this->applicationFileManager,
            applicationRepository: $this->applicationRepository,
        );

        return new CustomRuleValidator(
            translator: $this->translator,
            data: $data,
            rules: $rules,
            validationContext: $validationContext,
            serviceContainer: $serviceContainer,
        );
    }

    public function getSubsidyStageValidator(
        ApplicationStage $applicationStage,
        array $fieldValues,
        array $data,
        array $rules,
    ): SubsidyStageValidator {
        $validator = $this->getValidator(
            $applicationStage,
            $fieldValues,
            $data,
            $rules
        );

        return new SubsidyStageValidator($validator);
    }
}
