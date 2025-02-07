<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class PasswordResetEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2004';
    public const EVENT_KEY = 'reset_password';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_UPDATE;
    }
}
