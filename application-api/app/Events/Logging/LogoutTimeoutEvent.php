<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class LogoutTimeoutEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '1003';
    public const EVENT_KEY = 'logout_timeout';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_EXECUTE;
    }
}
