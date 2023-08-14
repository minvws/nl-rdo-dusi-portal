<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

readonly class AnswersByApplicationStage
{
    /**
     * @param array<ApplicationStageAnswers> $stages
     */
    public function __construct(public array $stages)
    {
    }
}
