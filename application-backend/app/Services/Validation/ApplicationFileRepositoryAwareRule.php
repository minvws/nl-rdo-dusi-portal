<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;

interface ApplicationFileRepositoryAwareRule
{
    /**
     * Set the ApplicationFileRepository under validation.
     * @param ApplicationFileRepository $applicationFileService
     * @return void
     */
    public function setApplicationFileRepository(ApplicationFileRepository $applicationFileService): void;
}
