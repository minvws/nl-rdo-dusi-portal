<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class ApplicationFileService
{
    public function __construct(
        protected Filesystem $filesystem,
    ) {
    }

    public function writeFile(ApplicationStage $applicationStage, Field $field, string $contents): bool
    {
        return $this->filesystem->put($this->getFilePath($applicationStage, $field), $contents);
    }

    public function fileExists(ApplicationStage $applicationStage, Field $field): bool
    {
        return $this->filesystem->exists($this->getFilePath($applicationStage, $field));
    }

    protected function getFilePath(ApplicationStage $applicationStage, Field $field): string
    {
        return sprintf('%s/%s', $applicationStage->id, $field->code);
    }
}
