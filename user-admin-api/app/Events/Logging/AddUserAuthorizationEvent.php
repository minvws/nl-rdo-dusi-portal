<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class AddUserAuthorizationEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '2044';
    public const EVENT_KEY = 'add_user_authorization';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_CREATE;
    }
}
