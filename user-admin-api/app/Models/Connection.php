<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Models;

final readonly class Connection
{
    public const APPLICATION = 'pgsql_application';
    public const USER = 'pgsql_user';

    private function __construct()
    {
    }
}
