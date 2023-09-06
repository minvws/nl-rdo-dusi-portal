<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\DTO;

use DateTime;

readonly class LetterData
{
    public function __construct(
        public string $subsidyTitle,
        public string $decision,
        public ApplicationStages $stages,
        public DateTime $createdAt,
        public string $contactEmailAddress,
        public ?string $reference,
        public ?string $applicationCode = null,
        public ?string $cssPath = null,
        public ?string $logoPath = null,
    ) {
    }

    public function getStage(string $key): ?ApplicationStageData
    {
        return $this->stages->$key;
    }

    public function __get(string $key): ?ApplicationStageData
    {
        return $this->getStage($key);
    }
}
