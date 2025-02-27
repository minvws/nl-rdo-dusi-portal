<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Bridge\Shared\Connection;

class ConnectionManager
{
    /** @var array<string, Connection> */
    private array $connections = [];

    public function __construct(private readonly ConfigContract $config)
    {
    }

    private function getDefaultConnectionName(): string
    {
        return $this->config->get('bridge.defaultConnection', 'default');
    }

    /**
     * @param string $name
     * @return array<string, mixed>
     */
    private function configuration(string $name): array
    {
        $connections = $this->config->get('bridge.connections', []);
        assert(is_array($connections));

        $config = Arr::get($connections, $name);
        if (is_null($config)) {
            throw new InvalidArgumentException("Connection [{$name}] not configured.");
        }

        return $config;
    }

    public function connection(?string $name = null): Connection
    {
        $name = $name ?: $this->getDefaultConnectionName();

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        $config = $this->configuration($name);
        assert(is_string($config['host']));
        assert(is_int($config['port']));
        assert(is_string($config['user']));
        assert(is_string($config['password']));
        assert(is_string($config['queue']));

        $connection =
            Connection::create(
                $config['host'],
                $config['port'],
                $config['user'],
                $config['password'],
                $config['queue']
            );

        $this->connections[$name] = $connection;

        return $connection;
    }

    public function client(?string $connection = null): Client
    {
        return $this->connection($connection)->client();
    }
}
