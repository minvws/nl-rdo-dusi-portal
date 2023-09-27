<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTimeInterface;

readonly class LetterData
{
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
        public ?string $cssPath = null,
        public ?string $logoPath = null,
        public ?string $signaturePath = null,
    ) {
    }

    public function getStage(string $key): ?LetterStageData
    {
        return $this->stages->get($key);
    }

    public function __get(string $key): ?LetterStageData
    {
        return $this->getStage($key);
    }
}
