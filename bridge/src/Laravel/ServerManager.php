<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use MinVWS\Codable\Coding\Codable;
use MinVWS\DUSi\Shared\Bridge\Server\Server;
use ReflectionMethod;

class ServerManager
{
    /** @var array<string, Server> */
    private array $servers = [];

    public function __construct(
        private readonly Application $app,
        private readonly ConnectionManager $connectionManager
    ) {
    }

    private function getDefaultServerName(): string
    {
        return $this->app['config']['bridge.defaultServer'] ?? 'default';
    }

    private function configuration(string $name): array
    {
        $servers = $this->app['config']['bridge.servers'] ?? [];
        assert(is_array($servers));

        $config = Arr::get($servers, $name);
        if (is_null($config)) {
            throw new InvalidArgumentException("Server [{$name}] not configured.");
        }


        return $config;
    }

    public function server(?string $name = null): Server
    {
        $name = $name ?: $this->getDefaultServerName();

        if (isset($this->servers[$name])) {
            return $this->servers[$name];
        }

        $config = $this->configuration($name);

        assert(!isset($config['connection']) || is_string($config['connection']));
        $connection = $this->connectionManager->connection($config['connection'] ?? null);

        $server = new Server($connection);

        assert(is_array($config['bindings']));
        foreach ($config['bindings'] as $method => $binding) {
            assert(is_string($binding['paramsClass']));
            assert(is_a($binding['paramsClass'], Codable::class, true));
            assert(isset($binding['callback']));
            $server->bind(
                $method,
                $binding['paramsClass'],
                fn ($params) => $this->invokeCallback($binding['callback'], $params)
            );
        }

        return $server;
    }


    private function invokeCallback(mixed $callback, Codable $params): Codable
    {
        if (is_array($callback) && count($callback) === 2) {
            $method = new ReflectionMethod(...$callback);
            if (!$method->isStatic()) {
                $callback[0] = $this->app->make($callback[0]);
            }
        }

        $result = $this->app->call($callback, [$params]);
        assert($result instanceof Codable);
        return $result;
    }
}
