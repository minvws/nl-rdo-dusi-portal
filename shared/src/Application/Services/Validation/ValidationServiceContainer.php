<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;

class ValidationServiceContainer
{
    public function __construct(
        private ApplicationFileManager $applicationFileManager,
        private ApplicationRepository $applicationRepository
    ) {
    }

    public function getApplicationFileManager(): ApplicationFileManager
    {
        return $this->applicationFileManager;
    }

    public function getApplicationRepository(): ApplicationRepository
    {
        return $this->applicationRepository;
    }
}
