<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Events\Logging;

class ViewUserEvent extends UserAdminLogEvent
{
    public const EVENT_CODE = '2042';
    public const EVENT_KEY = 'view_user';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
