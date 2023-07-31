<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Shared\Models;

final readonly class Connection
{
    public const FORM = 'pgsql_form';

    public const APPLICATION = 'pgsql_application';

    private function __construct()
    {
    }
}
