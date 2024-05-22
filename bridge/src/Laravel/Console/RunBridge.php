<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use MinVWS\DUSi\Shared\Bridge\Laravel\ServerManager;

class RunBridge extends Command
{
    protected $signature = 'bridge:run {--server=}';
    protected $description = 'Runs the RabbitMQ RPC bridge';

    public function handle(ServerManager $serverManager): void
    {
        $name = $this->option('server');
        if (!is_string($name) && $name !== null) {
            throw new InvalidArgumentException('Server must be a string or null');
        }

        $server = $serverManager->server($name);
        $server->run();
    }
}
