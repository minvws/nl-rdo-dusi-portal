<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Laravel\Console;

use DateTimeImmutable;
use Illuminate\Console\Command;
use InvalidArgumentException;
use MinVWS\DUSi\Shared\Bridge\Laravel\ConnectionManager;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Ping;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Pong;

class PingBridge extends Command
{
    protected $signature = 'bridge:ping {--connection=}';
    protected $description = 'Pings the RabbitMQ RPC bridge';

    public function handle(ConnectionManager $connectionManager): void
    {
        $name = $this->option('connection');
        if (!is_string($name) && $name !== null) {
            throw new InvalidArgumentException('Connection must be a string or null');
        }

        $client = $connectionManager->client($name);
        $result = $client->call('ping', new Ping(new DateTimeImmutable()), Pong::class, timeout: 5);

        $diffTTS = $result->requestStamp->diff($result->responseStamp);
        $diffRTT = $result->requestStamp->diff(new DateTimeImmutable());
        $this->output->writeln(sprintf('Time to server: %.6f', $diffTTS->s + $diffTTS->f));
        $this->output->writeln(sprintf('Round-trip time (RTT): %.6f', $diffRTT->s + $diffRTT->f));
    }
}
