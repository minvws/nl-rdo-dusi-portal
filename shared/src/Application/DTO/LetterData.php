<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTimeInterface;

readonly class LetterData
{
    private const PUBLIC_PATH = __DIR__ . '/../../../public';
    private const RESOURCE_PATH = __DIR__ . '/../../../resources';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public string $subsidyTitle,
        public LetterStages $stages,
        public DateTimeInterface $createdAt,
        public string $contactEmailAddress,
        public string $reference,
        public ?string $motivation,
        public ?string $applicationCode = null,
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
            'logoStream' => $this->getImageStream('vws_dusi_logo.svg'),
            'signatureStream' => $this->getImageStream('vws_dusi_signature.jpg'),
            default => $this->getStage($key),
        };
    }

    private function getStylesheetPath(): string
    {
        $manifestContent = file_get_contents(self::PUBLIC_PATH . '/build/manifest.json');
        $manifest = json_decode($manifestContent, true);

        return realpath(self::PUBLIC_PATH . '/build/' . $manifest['resources/scss/pdf.scss']['file']);
    }

    private function getImageStream(string $image): string
    {
        return file_get_contents(self::RESOURCE_PATH . "/img/{$image}");
    }
}
