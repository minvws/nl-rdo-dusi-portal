<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class ViewApplicationEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1016';
    public const EVENT_KEY = 'view_application';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
