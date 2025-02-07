<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Events\Logging;

class UpdateUserEvent extends UserAdminLogEvent
{
    public const EVENT_CODE = '2043';
    public const EVENT_KEY = 'update_user';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_UPDATE;
    }
}
