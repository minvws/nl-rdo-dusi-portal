<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class ListApplicationsEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1015';
    public const EVENT_KEY = 'list_applications';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
