<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Events\Logging;

class DeleteUserAuthorizationEvent extends UserAdminLogEvent
{
    public const EVENT_CODE = '2045';
    public const EVENT_KEY = 'delete_user_authorization';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_DELETE;
    }
}
