<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Shared;

use Exception;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection
{
    private ?Client $client = null;

    public function __construct(
        public readonly AMQPStreamConnection $connection,
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
        return new self(new AMQPStreamConnection($host, $port, $user, $password), $queue);
    }

    public function client(): Client
    {
        if (!isset($this->client)) {
            $this->client = new Client($this);
        }

        return $this->client;
    }
}
