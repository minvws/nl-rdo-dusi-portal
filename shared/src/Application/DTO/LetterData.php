<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTimeInterface;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Services\SubsidyFileManager;

readonly class LetterData
{
    private const PUBLIC_PATH = __DIR__ . '/../../../public';
    private const RESOURCE_PATH = __DIR__ . '/../../../resources';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public Subsidy $subsidy,
        public LetterStages $stages,
        public DateTimeInterface $createdAt,
        public string $contactEmailAddress,
        public string $reference,
        public DateTimeInterface $submittedAt,
        public SubsidyFileManager $fileManager,
        public ?DateTimeInterface $lastAllocatedAt,
    ) {
    }

    public function getStage(string $key): ?LetterStageData
    {
        return $this->stages->get($key);
    }

    public function __get(string $key): mixed
    {
        return match ($key) {
            'stylesheetPath' => $this->getStylesheetPath(),
            'logoStream' => $this->getImageStream('ro-logo.svg'),
            'subsidyTitle' => $this->subsidy->title,
            default => $this->getStage($key),
        };
    }

    private function getStylesheetPath(): string
    {
        $manifestContent = file_get_contents(self::PUBLIC_PATH . '/build/manifest.json');
        assert(is_string($manifestContent));
        $manifest = json_decode($manifestContent, true);
        $path = realpath(self::PUBLIC_PATH . '/build/' . $manifest['resources/scss/pdf.scss']['file']);
        assert(is_string($path));
        return $path;
    }

    private function getImageStream(string $image): string
    {
        $contents = file_get_contents(self::RESOURCE_PATH . "/img/{$image}");
        assert(is_string($contents));
        return $contents;
    }

    /**
     * @throws \Exception
     */
    private function getFileFromSubsidyDisk(string $fileId): string
    {
        $contents = $this->fileManager->readFile($this->subsidy, $fileId);

        assert(is_string($contents));

        return $contents;
    }

    public function getSignature(string $fileId): string
    {
        return $this->getFileFromSubsidyDisk($fileId);
    }
}
