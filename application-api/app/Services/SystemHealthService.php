<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Models\DTO\BridgeServiceHealth;
use MinVWS\DUSi\Shared\Application\Interfaces\ServiceHealth;
use MinVWS\DUSi\Shared\Application\DTO\RedisServiceHealth;
use MinVWS\DUSi\Shared\Bridge\Client\Client;

class SystemHealthService
{
    public function __construct(
        private readonly Client $bridgeClient
    ) {
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

    /**
     * @return ServiceHealth[]
     */
    private function collectServicesHealth(): array
    {
        return [
            new RedisServiceHealth('redis'),
            new BridgeServiceHealth('bridge', $this->bridgeClient),
        ];
    }
}
