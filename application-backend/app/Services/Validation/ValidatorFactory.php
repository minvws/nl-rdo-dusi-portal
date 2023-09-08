<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;

class ValidatorFactory
{
    public function __construct(
        protected ApplicationFileRepository $applicationFileService,
        protected ApplicationRepository $applicationRepository,
    ) {
    }

    /**
     * @param ApplicationStage $applicationStage
     * @param array<string, FieldValue> $fieldValues
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
        return new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: $data,
            rules: $rules,
            applicationStage: $applicationStage,
            fieldValues: $fieldValues,
            applicationFileRepository: $this->applicationFileService,
            applicationRepository: $this->applicationRepository,
        );
    }
}
