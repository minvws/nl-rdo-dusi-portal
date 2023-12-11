<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class StartApplicationEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1011';
    public const EVENT_KEY = 'start_application';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_EXECUTE;
    }
}
