<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class SaveApplicationEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1012';
    public const EVENT_KEY = 'save_application';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_UPDATE;
    }
}
