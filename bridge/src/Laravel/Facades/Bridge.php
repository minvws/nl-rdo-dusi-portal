<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Bridge\Laravel\BridgeManager;
use MinVWS\DUSi\Shared\Bridge\Laravel\ConnectionManager;
use MinVWS\DUSi\Shared\Bridge\Laravel\ServerManager;
use MinVWS\DUSi\Shared\Bridge\Server\Server;
use MinVWS\DUSi\Shared\Bridge\Shared\Connection;

/**
 * @method static ConnectionManager connectionManager()
 * @method static Connection connection(string|null $name = null)
 * @method static Client client(string|null $connection = null)
 * @method static ServerManager serverManager()
 * @method static Server server(string|null $name = null)
 */
class Bridge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BridgeManager::class;
    }
}
