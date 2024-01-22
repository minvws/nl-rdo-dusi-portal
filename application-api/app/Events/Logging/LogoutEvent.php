<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class LogoutEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '1002';
    public const EVENT_KEY = 'logout';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_EXECUTE;
    }
}
