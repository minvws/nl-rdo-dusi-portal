<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;

interface ApplicationFileManagerAwareRule
{
    public function setApplicationFileManager(ApplicationFileManager $applicationFileManager): void;
}
