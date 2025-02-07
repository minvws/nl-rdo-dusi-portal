<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

interface FieldHook
{
    public function isHookActive(ApplicationStage $applicationStage): bool;

    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue;
}
