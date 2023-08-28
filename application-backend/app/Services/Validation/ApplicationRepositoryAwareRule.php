<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;

interface ApplicationRepositoryAwareRule
{
    /**
     * Set the ApplicationRepository under validation.
     * @param ApplicationRepository $applicationRepository
     * @return void
     */
    public function setApplicationRepository(ApplicationRepository $applicationRepository): void;
}
