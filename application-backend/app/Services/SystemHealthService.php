<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Contracts\Redis\Connection;
use MinVWS\DUSi\Shared\Application\DTO\SurepayServiceHealth;
use MinVWS\DUSi\Shared\Application\DTO\RedisServiceHealth;
use MinVWS\DUSi\Shared\Application\Interfaces\ServiceHealth;

readonly class SystemHealthService
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @return array{healthy: bool, services: array<ServiceHealth>}
     */
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

    /**
     * @return array<ServiceHealth>
     */
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
