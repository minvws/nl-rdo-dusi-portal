<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class CreateUserEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '2041';
    public const EVENT_KEY = 'create_user';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_CREATE;
    }
}
