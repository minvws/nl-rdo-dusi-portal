<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Contracts\Redis\Connection;
use MinVWS\DUSi\Shared\Application\DTO\SurepayServiceHealth;
use MinVWS\DUSi\Shared\Application\DTO\RedisServiceHealth;

class SystemHealthService
{
    public function __construct(private readonly Connection $connection)
    {

    }
    public function getSystemHealthStatus(): array
    {
        $services = $this->collectServicesHealth();

        $overallHealthy = true;

        foreach ($services as $service) {
            if (!$service->isHealthy()) {
                $overallHealthy = false;
                break;
            }
        }

        return [
            'healthy' => $overallHealthy,
            'services' => $services,
        ];
    }

    private function collectServicesHealth(): array
    {
        $services = [];

        $redisHealth = new RedisServiceHealth('redis');

        $services[] = $redisHealth;

        if ($redisHealth->isHealthy()) {
            $services[] = new SurepayServiceHealth('surepay', $this->connection);
        }

        return $services;
    }
}
