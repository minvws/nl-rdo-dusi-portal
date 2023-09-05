<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Shared;

use Exception;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPConnectionFactory;

class Connection
{
    private ?Client $client = null;

    public function __construct(
        public readonly AbstractConnection $connection,
        public readonly string $queue = 'rpc_queue'
    ) {
    }

    /**
     * @throws Exception
     */
    public static function create(
        string $host = 'localhost',
        int $port = 5672,
        string $user = 'guest',
        string $password = 'guest',
        string $queue = 'rpc_queue'
    ): self {
        $config = new AMQPConnectionConfig();
        $config->setHost($host);
        $config->setPort($port);
        $config->setUser($user);
        $config->setPassword($password);
        $config->setIsLazy(true);
        return new self(AMQPConnectionFactory::create($config), $queue);
    }

    public function client(): Client
    {
        if (!isset($this->client)) {
            $this->client = new Client($this);
        }

        return $this->client;
    }
}
