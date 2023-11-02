<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class ListMessagesEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '1021';
    public const EVENT_KEY = 'list_messages';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
