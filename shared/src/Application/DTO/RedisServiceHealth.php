<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use RedisException;

class RedisServiceHealth extends AbstractServiceHealth
{
    protected function checkHealth(): void
    {
        $isHealthy = false;

        try {
            $isHealthy = Redis::ping();
        } catch (RedisException $e) {
            Log::error('Tried to ping Redis, but it failed', [$e]);
        }

        $this->isHealthy = $isHealthy;
    }
}
