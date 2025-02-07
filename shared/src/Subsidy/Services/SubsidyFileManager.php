<?php

/**
 * Application File Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Services;

use Exception;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyFileRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SubsidyFileManager
{
    public function __construct(
        private readonly SubsidyFileRepository $fileRepository,
    ) {
    }

    public function fileExists(Subsidy $subsidy, string $fileId): bool
    {
        $path = $this->getFilePath($subsidy, $fileId);

        return $this->fileRepository->fileExists($path);
    }

    /**
     * @throws Exception
     */
    public function readFile(Subsidy $subsidy, string $fileId): string
    {
        $filePath = $this->getFilePath($subsidy, $fileId);

        $file = $this->fileRepository->readFile($filePath);

        if (empty($file)) {
            throw new Exception('File content cannot be empty!');
        }

        return $file;
    }

    /**
     * @throws Exception
     */
    public function writeFile(Subsidy $subsidy, string $fileId, string $content): bool
    {
        if (empty($content)) {
            throw new Exception('File content cannot be empty!');
        }

        $filePath = $this->getFilePath($subsidy, $fileId);

        return $this->fileRepository->writeFile($filePath, $content);
    }

    public function deleteFile(Subsidy $subsidy, string $fileId): void
    {
        $file = $this->getFilePath($subsidy, $fileId);

        $this->fileRepository->deleteFile($file);
    }

    private function getFilePath(Subsidy $subsidy, string $id): string
    {
        return sprintf('%s/%s', $subsidy->id, $id);
    }
}
