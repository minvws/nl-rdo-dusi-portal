<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

interface ApplicationStageAwareRule
{
    /**
     * Set the ApplicationStageVersion under validation.
     * @param ApplicationStage $applicationStage
     * @return void
     */
    public function setApplicationStage(ApplicationStage $applicationStage): void;
}
