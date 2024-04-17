<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

abstract class ApplicationApiLogEvent extends GeneralLogEvent
{
    public string $source = 'application-api';
}
