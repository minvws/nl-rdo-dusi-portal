<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

abstract class UserAdminLogEvent extends GeneralLogEvent
{
    public string $source = 'user-admin-api';
}
