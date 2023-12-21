<?php

/**
 * Application File Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Exception;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationFileEncryptionService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationFileManager
{
    private const KEYINFO_FILE_EXTENSION = '.keyinfo';

    public function __construct(
        private readonly ApplicationFileEncryptionService $encryptionService,
        private readonly ApplicationFileRepository $fileRepository,
    ) {
    }

    public function fileExists(ApplicationStage $applicationStage, Field $field, string $fileId): bool
    {
        $path = $this->getFilePath($applicationStage, $field, $fileId);

        return $this->fileRepository->fileExists($path)
            && $this->fileRepository->fileExists($path . self::KEYINFO_FILE_EXTENSION);
    }

    public function readFile(ApplicationStage $applicationStage, Field $field, string $fileId): string
    {
        $file = $this->getFilePath($applicationStage, $field, $fileId);

        return $this->readEncryptedFile($file);
    }

    /**
     * @throws Exception
     */
    public function readEncryptedFile(string $filepath): string
    {
        $keyInfo = $this->fileRepository->readFile($filepath . self::KEYINFO_FILE_EXTENSION);
        if (empty($keyInfo)) {
            throw new Exception('File key info cannot be empty!');
        }

        $file = $this->fileRepository->readFile($filepath);
        if (empty($file)) {
            throw new Exception('File content cannot be empty!');
        }

        return $this->encryptionService->getEncrypter($keyInfo)->decryptString($file);
    }

    public function writeFile(ApplicationStage $applicationStage, Field $field, string $fileId, string $content): bool
    {
        $file = $this->getFilePath($applicationStage, $field, $fileId);

        return $this->writeEncryptedFile($file, $content);
    }

    /**
     * @throws Exception
     */
    public function writeEncryptedFile(string $filepath, string $content): bool
    {
        if (empty($content)) {
            throw new Exception('File content cannot be empty!');
        }

        [$keyInfo, $encrypter] = $this->encryptionService->generateKeyInfo();

        $fileWritten = $this->fileRepository->writeFile($filepath, $encrypter->encryptString($content));
        $fileKeyInfoWritten = $this->fileRepository->writeFile($filepath . self::KEYINFO_FILE_EXTENSION, $keyInfo);

        return $fileWritten && $fileKeyInfoWritten;
    }

    public function deleteFile(ApplicationStage $applicationStage, Field $field, string $fileId): void
    {
        $file = $this->getFilePath($applicationStage, $field, $fileId);

        $this->fileRepository->deleteFile($file);
        $this->fileRepository->deleteFile($file . self::KEYINFO_FILE_EXTENSION);
    }

    public function copyFiles(ApplicationStage $sourceStage, ApplicationStage $targetStage): bool
    {
        return $this->fileRepository->copyFiles(
            sourceDirectory: $this->getStageDirectory($sourceStage),
            targetDirectory: $this->getStageDirectory($targetStage)
        );
    }

    public function cleanUpUnusedFiles(ApplicationStage $applicationStage, FieldValue $value): void
    {
        $field = $value->field;
        if ($field->type !== FieldType::Upload) {
            return;
        }

        $fileList = $value->value;
        if (!($fileList instanceof FileList)) {
            return;
        }

        $unusedFileIds = $this->getUnusedFileIds(
            fieldDirectory: $this->getFieldDirectory($applicationStage, $field),
            usedIds: $fileList->getFileIds()
        );
        foreach ($unusedFileIds as $fileId) {
            $this->deleteFile($applicationStage, $field, $fileId);
        }
    }

    private function getUnusedFileIds(string $fieldDirectory, array $usedIds): array
    {
        $files = array_map(
            'basename',
            $this->fileRepository->getFiles($fieldDirectory)
        );

        // Remove .keyinfo files from list of files
        $files = array_filter($files, fn ($file) => !str_ends_with($file, self::KEYINFO_FILE_EXTENSION));

        return array_diff($files, $usedIds);
    }

    private function getStageDirectory(ApplicationStage $applicationStage): string
    {
        return $applicationStage->id;
    }

    private function getFieldDirectory(ApplicationStage $applicationStage, Field $field): string
    {
        return sprintf('%s/%s', $this->getStageDirectory($applicationStage), $field->code);
    }

    private function getFilePath(ApplicationStage $applicationStage, Field $field, string $id): string
    {
        return sprintf('%s/%s', $this->getFieldDirectory($applicationStage, $field), $id);
    }
}
