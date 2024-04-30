<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Events\Logging;

class LoginEvent extends ApplicationApiLogEvent
{
    public const EVENT_CODE = '1001';
    public const EVENT_KEY = 'login';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_EXECUTE;
    }
}
