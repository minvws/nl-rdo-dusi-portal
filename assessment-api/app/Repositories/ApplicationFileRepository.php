<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class ApplicationFileRepository
{
    // TODO: Move to Shared package

    public function __construct(
        protected Filesystem $filesystem,
    ) {
    }

    public function readFile(ApplicationStage $applicationStage, Field $field, string $id): ?string
    {
        return $this->filesystem->get($this->getFilePath($applicationStage, $field, $id));
    }

    public function writeFile(ApplicationStage $applicationStage, Field $field, string $id, string $contents): bool
    {
        return $this->filesystem->put($this->getFilePath($applicationStage, $field, $id), $contents);
    }

    public function unlinkFile(ApplicationStage $applicationStage, Field $field, string $id): bool
    {
        return $this->filesystem->delete($this->getFilePath($applicationStage, $field, $id));
    }

    public function unlinkUnusedFiles(ApplicationStage $applicationStage, Field $field, array $usedIds): void
    {
        $files = array_map(
            'basename',
            $this->filesystem->files($this->getFieldPath($applicationStage, $field))
        );

        $unusedIds = array_diff($files, $usedIds);

        foreach ($unusedIds as $unusedId) {
            $this->unlinkFile($applicationStage, $field, $unusedId);
        }
    }

    public function fileExists(ApplicationStage $applicationStage, Field $field, string $id): bool
    {
        return $this->filesystem->exists($this->getFilePath($applicationStage, $field, $id));
    }

    protected function getFieldPath(ApplicationStage $applicationStage, Field $field): string
    {
        return sprintf('%s/%s', $applicationStage->id, $field->code);
    }

    protected function getFilePath(ApplicationStage $applicationStage, Field $field, string $id): string
    {
        return sprintf('%s/%s', $this->getFieldPath($applicationStage, $field), $id);
    }
}
