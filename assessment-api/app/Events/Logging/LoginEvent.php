<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class LoginEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2001';
    public const EVENT_KEY = 'login';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_EXECUTE;
    }
}
