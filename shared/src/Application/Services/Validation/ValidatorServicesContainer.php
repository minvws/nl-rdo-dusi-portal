<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;

class ValidatorServicesContainer
{
    public function __construct(
        private ApplicationStage $applicationStage,
        private array $fieldValues,
        private ApplicationFileManager $applicationFileManager,
        private ApplicationRepository $applicationRepository
    ) {
    }

    public function getApplicationStage(): ApplicationStage
    {
        return $this->applicationStage;
    }

    public function getFieldValues(): array
    {
        return $this->fieldValues;
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
