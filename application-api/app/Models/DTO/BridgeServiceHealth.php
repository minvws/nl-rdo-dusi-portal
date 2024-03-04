<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models\DTO;

use DateTimeImmutable;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\DTO\AbstractServiceHealth;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Ping;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Pong;

class BridgeServiceHealth extends AbstractServiceHealth
{
    public function __construct(string $name, private readonly Client $client)
    {
        parent::__construct($name);
    }

    protected function checkHealth(): void
    {
        $isHealthy = false;
        try {
            $result = $this->client->call('ping', new Ping(new DateTimeImmutable()), Pong::class, timeout: 5);
            $isHealthy = $result instanceof Pong;
        } catch (\Exception $e) {
            Log::error('Trying to ping bridge but failed', [$e]);
        }

        $this->isHealthy = $isHealthy;
    }
}
