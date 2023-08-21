<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Models;

final readonly class Connection
{
    public const FORM = 'pgsql_form';
    public const APPLICATION = 'pgsql_application';
    public const USER = 'pgsql_user';

    private function __construct()
    {
    }
}
