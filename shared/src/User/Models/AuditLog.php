<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Models;

use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\Logging\Laravel\Models\AuditLog as BaseModel;


class AuditLog extends BaseModel
{
    protected $connection = Connection::APPLICATION;
}
