<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

class ApplicationStageData
{
    public function __construct(
        public readonly ApplicationStage $applicationStage,
        public readonly object $data
    ) {
    }
}
