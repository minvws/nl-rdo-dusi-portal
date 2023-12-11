<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class ViewMessageEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1022';
    public const EVENT_KEY = 'view_message';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
