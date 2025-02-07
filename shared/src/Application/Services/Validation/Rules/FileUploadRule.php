<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationFileManagerAwareRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationStageAwareRule;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class FileUploadRule implements
    ValidationRule,
    ApplicationStageAwareRule,
    ApplicationFileManagerAwareRule
{
    protected ApplicationStage $applicationStage;
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
        if ($this->fieldNotRequiredAndValueIsNull($value)) {
            return;
        }

        if ($this->valueIsNotFileList($value)) {
            $fail("Field is required!");
            return;
        }

        if ($this->fieldIsNotRequiredAndFileListIsEmpty($value)) {
            return;
        }

        if ($this->aFileIsMissing($value)) {
            $fail("File not found!");
            return;
        }

        if (!$this->minItemsIsMet($value)) {
            $fail("Minimum number of files not met!");
            return;
        }

        if (!$this->maxItemsIsMet($value)) {
            $fail("Maximum number of files exceeded!");
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

    protected function maxItemsIsMet(FileList $value): bool
    {
        $maxItems = $this->field->params['maxItems'] ?? null;
        return $maxItems === null || $value->count() <= $maxItems;
    }

    protected function minItemsIsMet(FileList $value): bool
    {
        $minItems = $this->field->params['minItems'] ?? null;
        return $minItems === null || $value->count() >= $minItems;
    }

    protected function aFileIsMissing(FileList $value): bool
    {
        foreach ($value->items as $file) {
            $fileMissing = !$this->applicationFileManager->fileExists(
                applicationStage: $this->applicationStage,
                field: $this->field,
                fileId: $file->id,
            );
            if ($fileMissing) {
                return true;
            }
        }
        return false;
    }

    protected function fieldNotRequiredAndValueIsNull(mixed $value): bool
    {
        return !$this->field->is_required && $value === null;
    }

    protected function valueIsNotFileList(mixed $value): bool
    {
        return !($value instanceof FileList);
    }

    protected function fieldIsNotRequiredAndFileListIsEmpty(FileList $value): bool
    {
        return !$this->field->is_required && $value->count() === 0;
    }
}
