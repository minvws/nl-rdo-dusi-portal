<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationFileManagerAwareRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationRepositoryAwareRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationStageAwareRule;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class FileUploadRule implements
    ValidationRule,
    ApplicationStageAwareRule,
    ApplicationFileManagerAwareRule,
    ApplicationRepositoryAwareRule
{
    protected ApplicationStage $applicationStage;
    protected ApplicationRepository $applicationRepository;
    protected ApplicationFileManager $applicationFileManager;

    public function __construct(protected Field $field)
    {
    }

    /**
     * Run the validation rule.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) no need to check $value because answer should already exist
     * @param string $attribute
     * @param mixed $value should be FileList
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fieldIsRequired = $this->field->is_required;
        if (!$fieldIsRequired && $value === null) {
            return;
        }

        if ($fieldIsRequired && $value === null) {
            $fail("Field is required!");
            return;
        }

        if (!($value instanceof FileList)) {
            $fail("Value is not a FileList!");
            return;
        }

        foreach ($value->items as $file) {
            $fileExists = $this->applicationFileManager->fileExists(
                applicationStage: $this->applicationStage,
                field: $this->field,
                fileId: $file->id,
            );
            if (!$fileExists) {
                $fail("File not found!");
            }
        }
    }

    public function setApplicationStage(ApplicationStage $applicationStage): void
    {
        $this->applicationStage = $applicationStage;
    }

    public function setApplicationFileManager(ApplicationFileManager $applicationFileManager): void
    {
        $this->applicationFileManager = $applicationFileManager;
    }

    public function setApplicationRepository(ApplicationRepository $applicationRepository): void
    {
        $this->applicationRepository = $applicationRepository;
    }
}
