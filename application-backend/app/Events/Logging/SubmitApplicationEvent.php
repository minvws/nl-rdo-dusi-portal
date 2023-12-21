<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class SubmitApplicationEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1013';
    public const EVENT_KEY = 'submit_application';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_UPDATE;
    }
}
