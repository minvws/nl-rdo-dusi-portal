<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use MinVWS\DUSi\Application\Backend\Services\ApplicationFileService;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationFileServiceAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationRepositoryAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationStageVersionAwareRule;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class FileUploadRule implements
    ValidationRule,
    ApplicationStageVersionAwareRule,
    ApplicationFileServiceAwareRule,
    ApplicationRepositoryAwareRule
{
    protected ApplicationStageVersion $applicationStageVersion;
    protected ApplicationRepository $applicationRepository;
    protected ApplicationFileService $applicationFileService;

    public function __construct(protected Field $field)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value The value under validation does not need to be checked because answer should already exist.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fieldIsRequired = $this->field->is_required;

        $answer = $this->applicationRepository->getAnswer($this->applicationStageVersion, $this->field);
        if ($fieldIsRequired && $answer === null) {
            $fail("Field is required!");
        }

        $fileExists = $this->applicationFileService->fileExists(
            applicationStage: $this->applicationStageVersion->applicationStage,
            field: $this->field,
        );
        if (($answer || $fieldIsRequired) && !$fileExists) {
            $fail("File not found!");
        }
    }

    public function setApplicationStageVersion(ApplicationStageVersion $applicationStageVersion): void
    {
        $this->applicationStageVersion = $applicationStageVersion;
    }

    public function setApplicationFileService(ApplicationFileService $applicationFileService): void
    {
        $this->applicationFileService = $applicationFileService;
    }

    public function setApplicationRepository(ApplicationRepository $applicationRepository): void
    {
        $this->applicationRepository = $applicationRepository;
    }
}
