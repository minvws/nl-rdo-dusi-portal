<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class LogoutEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2002';
    public const EVENT_KEY = 'logout';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_EXECUTE;
    }
}
