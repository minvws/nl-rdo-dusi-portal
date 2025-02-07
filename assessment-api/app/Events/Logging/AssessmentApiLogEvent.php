<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

abstract class AssessmentApiLogEvent extends GeneralLogEvent
{
    public string $source = 'assessment-api';
}
