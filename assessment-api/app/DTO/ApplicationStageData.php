<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\DTO;

class ApplicationStageData
{
    /**
     * @var array<ApplicationStageAnswer>
     */
    protected array $stageAnswers = [];

    public function __construct(
        public readonly string $stageKey
    ) {
    }

    public function addStageAnswer(ApplicationStageAnswer $answer): void
    {
        $this->stageAnswers[$answer->answerKey] = $answer;
    }

    public function getStageAnswer(string $key): ?ApplicationStageAnswer
    {
        return $this->stageAnswers[$key] ?? null;
    }

    public function getAnswerData(string $key): mixed
    {
        return $this->stageAnswers[$key]->answerData ?? null;
    }

    public function __get(string $key): mixed
    {
        if (!array_key_exists($key, $this->stageAnswers)) {
            return null;
        }

        return $this->stageAnswers[$key]->answerData;
    }

    public function __set(string $key, ApplicationStageAnswer $answer): void
    {
        $this->stageAnswers[$key] = $answer;
    }
}
