<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;

readonly class ApplicationStageAnswers
{
    /**
     * @param array<Answer> $answers
     */
    public function __construct(
        public ApplicationStage $stage,
        public ApplicationStageVersion $stageVersion,
        public array $answers
    ) {
    }
}
