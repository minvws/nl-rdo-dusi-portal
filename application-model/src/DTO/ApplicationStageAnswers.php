<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

class ApplicationStageAnswers
{
    /**
     * @param array<Answer> $answers
     */
    public function __construct(
        public readonly ApplicationStage $stage,
        public readonly array $answers
    ) {
    }
}
