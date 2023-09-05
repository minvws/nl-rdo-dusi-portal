<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\DTO;

class ApplicationStages
{
    /**
     * @param array<ApplicationStageData> $stages
     */
    protected array $stages = [];

    public function addStage(ApplicationStageData $stageData): void
    {
        $this->stages[$stageData->stageKey] = $stageData;
    }

    public function getStage(string $stageKey): ?ApplicationStageData
    {
        return $this->stages[$stageKey] ?? null;
    }

    public function __get(string $key): ?ApplicationStageData
    {
        return $this->getStage($key);
    }

    public function __set(string $key, ApplicationStageData $stageData): void
    {
        $this->stages[$key] = $stageData;
    }
}
