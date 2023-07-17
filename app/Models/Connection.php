<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace App\Models;

final readonly class Connection
{
    public const FORM = 'pgsql_form';
    public const USER = 'pgsql_user';

    private function __construct()
    {
    }
}
