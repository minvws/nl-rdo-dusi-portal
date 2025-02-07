<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;

class ApplicationFileRepository
{
    public function __construct(
        protected Filesystem $filesystem,
    ) {
    }

    public function readFile(string $filePath): ?string
    {
        return $this->filesystem->get($filePath);
    }

    public function writeFile(string $filePath, string $contents): bool
    {
        return $this->filesystem->put($filePath, $contents);
    }

    public function deleteFile(string $filePath): bool
    {
        return $this->filesystem->delete($filePath);
    }

    public function getFiles(string $directory, bool $recursive = false): array
    {
        return $this->filesystem->files($directory, $recursive);
    }

    public function fileExists(string $filePath): bool
    {
        return $this->filesystem->exists($filePath);
    }

    public function makeDirectory(string $filePath): bool
    {
        return $this->filesystem->makeDirectory($filePath);
    }

    public function deleteDirectory(string $directoryPath): bool
    {
        return $this->filesystem->deleteDirectory($directoryPath);
    }

    public function copyFiles(string $sourceDirectory, string $targetDirectory): bool
    {
        if (!$this->filesystem->directoryExists($sourceDirectory)) {
            return true; // nothing to clone
        }

        foreach ($this->getFiles($sourceDirectory, true) as $filePath) {
            $targetPath = $this->targetPath($filePath, $sourceDirectory, $targetDirectory);
            if (!$this->filesystem->copy($filePath, $targetPath)) {
                return false;
            }
        }

        return true;
    }

    protected function targetPath(
        string $path,
        string $sourcePath,
        string $targetPath,
    ): string {
        return $targetPath . substr($path, strlen($sourcePath));
    }
}
