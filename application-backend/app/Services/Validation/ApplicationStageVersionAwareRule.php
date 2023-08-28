<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;

interface ApplicationStageVersionAwareRule
{
    /**
     * Set the ApplicationStageVersion under validation.
     * @param ApplicationStageVersion $applicationStageVersion
     * @return void
     */
    public function setApplicationStageVersion(ApplicationStageVersion $applicationStageVersion): void;
}
