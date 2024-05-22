<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use MinVWS\Codable\Coding\Codable;
use MinVWS\DUSi\Shared\Bridge\Server\JSONMethodHandler;
use MinVWS\DUSi\Shared\Bridge\Server\Server;
use Psr\Log\LoggerInterface;
use ReflectionMethod;
use RuntimeException;

class ServerManager
{
    /** @var array<string, Server> */
    private array $servers = [];

    public function __construct(
        private readonly Application $app,
        private readonly ConfigContract $config,
        private readonly ConnectionManager $connectionManager,
        private readonly LoggerInterface $logger
    ) {
    }

    private function getDefaultServerName(): string
    {
        return $this->config->get('bridge.defaultServer', 'default');
    }

    /**
     * @param string $name
     * @return array{
     *     connection: string|null,
     *     bindings: array<string, array{paramsClass: class-string<Codable>, callback: callable}>
     * }
     */
    private function configuration(string $name): array
    {
        $servers = $this->config->get('bridge.servers', []);
        if (!is_array($servers)) {
            throw new RuntimeException('Servers configuration must be an array.');
        }

        $config = Arr::get($servers, $name);
        $this->validateServerConfig($name, $config);

        return $config;
    }

    public function server(?string $name = null): Server
    {
        $name = $name ?: $this->getDefaultServerName();

        if (isset($this->servers[$name])) {
            return $this->servers[$name];
        }

        $declareExchangeAndQueue = $this->config->get('bridge.declare_exchange_and_queue', true);

        $config = $this->configuration($name);
        $connection = $this->connectionManager->connection($config['connection']);

        $server = new Server(
            connection: $connection,
            logger: $this->logger,
            jsonMethodHandler: new JSONMethodHandler(),
            declareQueue: $declareExchangeAndQueue,
        );
        foreach ($config['bindings'] as $method => $binding) {
            $server->bind(
                $method,
                $binding['paramsClass'],
                fn (Codable $params) => $this->invokeCallback($binding['callback'], $params)
            );
        }

        $this->servers[$name] = $server;
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

        $result = call_user_func($callback, $params);
        assert($result instanceof Codable);
        return $result;
    }

    /**
     * @param string $name
     * @param array<mixed>|null $config
     * @return void
     */
    private function validateServerConfig(string $name, array|null $config): void
    {
        if (is_null($config)) {
            throw new RuntimeException("Server [{$name}] not configured.");
        }

        if (!is_array($config)) {
            throw new RuntimeException("Server [{$name}] configuration must be an array.");
        }

        if (!isset($config['connection']) || !is_string($config['connection'])) {
            throw new RuntimeException("Server [{$name}] configuration must have a connection key.");
        }

        $this->validateServerBindings($name, $config['bindings'] ?? null);
    }

    private function validateServerBindings(string $name, mixed $bindings): void
    {
        if (!is_array($bindings)) {
            throw new RuntimeException("Server [{$name}] bindings must be an array.");
        }

        foreach ($bindings as $method => $binding) {
            if (!is_array($binding)) {
                throw new RuntimeException("Server binding [{$method}] must be an array.");
            }

            if (!isset($binding['paramsClass']) || !is_string($binding['paramsClass'])) {
                throw new RuntimeException("Server binding [{$method}] must have a paramsClass key.");
            }

            if (!is_a($binding['paramsClass'], Codable::class, true)) {
                throw new RuntimeException("Server binding [{$method}] paramsClass must implement Codable.");
            }

            if (!isset($binding['callback']) || !is_callable($binding['callback'], syntax_only: true)) {
                throw new RuntimeException("Server binding [{$method}] must have a callback key.");
            }
        }
    }
}
