<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

abstract class ApplicationBackendLogEvent extends GeneralLogEvent
{
    public string $source = 'application-backend';
}
