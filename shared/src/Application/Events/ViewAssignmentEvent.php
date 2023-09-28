<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Events;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class ViewAssignmentEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '2012';
    public const EVENT_KEY = 'view_assignment';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
