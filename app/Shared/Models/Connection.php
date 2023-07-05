<?php
declare(strict_types=1);

namespace App\Shared\Models;

final readonly class Connection
{
    public const Form = 'pgsql-form';
    public const Application = 'pgsql-application';

    private function __construct()
    {
    }
}
