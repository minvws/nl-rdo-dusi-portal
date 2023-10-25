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
     * @return Validator
     */
    public function getValidator(
        ApplicationStage $applicationStage,
        array $fieldValues,
        array $data,
        array $rules,
    ): Validator {
        $validatorServicesContainer = new ValidatorServicesContainer(
            applicationStage: $applicationStage,
            fieldValues: $fieldValues,
            applicationFileManager: $this->applicationFileManager,
            applicationRepository: $this->applicationRepository,
        );

        return new Validator(
            translator: $this->translator,
            data: $data,
            rules: $rules,
            servicesContainer: $validatorServicesContainer,
        );
    }
}
