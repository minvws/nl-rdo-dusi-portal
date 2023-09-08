<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationFileRepositoryAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationRepositoryAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationStageAwareRule;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class FileUploadRule implements
    ValidationRule,
    ApplicationStageAwareRule,
    ApplicationFileRepositoryAwareRule,
    ApplicationRepositoryAwareRule
{
    protected ApplicationStage $applicationStage;
    protected ApplicationRepository $applicationRepository;
    protected ApplicationFileRepository $applicationFileRepository;

    public function __construct(protected Field $field)
    {
    }

    /**
     * Run the validation rule.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) no need to check $value because answer should already exist
     * @param string $attribute
     * @param mixed $value The value under validation does not need to be checked because answer should already exist.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fieldIsRequired = $this->field->is_required;

        $answer = $this->applicationRepository->getAnswer($this->applicationStage, $this->field);
        if (!$fieldIsRequired && $answer === null) {
            return;
        }

        if ($fieldIsRequired && $answer === null) {
            $fail("Field is required!");
            return;
        }

        $fileExists = $this->applicationFileRepository->fileExists(
            applicationStage: $this->applicationStage,
            field: $this->field,
        );
        if (!$fileExists) {
            $fail("File not found!");
        }
    }

    public function setApplicationStage(ApplicationStage $applicationStage): void
    {
        $this->applicationStage = $applicationStage;
    }

    public function setApplicationFileRepository(ApplicationFileRepository $applicationFileService): void
    {
        $this->applicationFileRepository = $applicationFileService;
    }

    public function setApplicationRepository(ApplicationRepository $applicationRepository): void
    {
        $this->applicationRepository = $applicationRepository;
    }
}
