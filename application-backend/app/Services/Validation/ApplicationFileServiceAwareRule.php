<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use MinVWS\DUSi\Application\Backend\Services\ApplicationFileService;

interface ApplicationFileServiceAwareRule
{
    /**
     * Set the ApplicationFileService under validation.
     * @param ApplicationFileService $applicationFileService
     * @return void
     */
    public function setApplicationFileService(ApplicationFileService $applicationFileService): void;
}
