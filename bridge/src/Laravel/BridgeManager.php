<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel;

use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Bridge\Server\Server;
use MinVWS\DUSi\Shared\Bridge\Shared\Connection;

class BridgeManager
{
    public function __construct(
        private readonly ConnectionManager $connectionManager,
        private readonly ServerManager $serverManager
    ) {
    }

    public function connectionManager(): ConnectionManager
    {
        return $this->connectionManager;
    }

    public function connection(?string $name = null): Connection
    {
        return $this->connectionManager()->connection($name);
    }

    public function client(?string $connection = null): Client
    {
        return $this->connectionManager()->client($connection);
    }

    public function serverManager(): ServerManager
    {
        return $this->serverManager;
    }

    public function server(?string $name = null): Server
    {
        return $this->serverManager()->server($name);
    }
}
