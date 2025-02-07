<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

class AnswersByApplicationStage
{
    /**
     * @param array<ApplicationStageAnswers> $stages
     */
    public function __construct(public readonly array $stages)
    {
    }
}
