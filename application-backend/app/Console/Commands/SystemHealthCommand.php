<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\Command;
use MinVWS\DUSi\Application\Backend\Services\SystemHealthService;

class SystemHealthCommand extends Command
{
    protected $signature = 'noc:health';

    protected $description = 'Returns overall health of backend';

    public function handle(SystemHealthService $systemHealthService): int
    {
        $systemHealthStatus = $this->systemHealthService->getSystemHealthStatus();
        $state = json_encode($systemHealthStatus, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        $this->info($state);

        return !$systemHealthStatus['healthy'] ? self::FAILURE : self::SUCCESS;
    }
}
