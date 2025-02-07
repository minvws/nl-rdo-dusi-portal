<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Events\Logging;

class AddUserAuthorizationEvent extends UserAdminLogEvent
{
    public const EVENT_CODE = '2044';
    public const EVENT_KEY = 'add_user_authorization';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_CREATE;
    }
}
