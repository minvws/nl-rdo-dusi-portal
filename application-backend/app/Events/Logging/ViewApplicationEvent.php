<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class ViewApplicationEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '1016';
    public const EVENT_KEY = 'view_application';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
