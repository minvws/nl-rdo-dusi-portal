<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class ListMessagesEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1021';
    public const EVENT_KEY = 'list_messages';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
