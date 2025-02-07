<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

class DeleteApplicationEvent extends ApplicationBackendLogEvent
{
    public const EVENT_CODE = '1014';
    public const EVENT_KEY = 'delete_application';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_DELETE;
    }
}
