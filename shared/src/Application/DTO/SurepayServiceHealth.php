<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Contracts\Redis\Connection;
use Redis;

class SurepayServiceHealth extends AbstractServiceHealth
{
    private const SUREPAY_FAILED_COUNTER_KEY = 'surepay_failed_counter';
    private const SUREPAY_FAIL_EXPIRY_MINUTES = 15;

    public function __construct(string $name, private readonly Connection $connection)
    {
        parent::__construct($name);
    }

    protected function checkHealth(): void
    {
        // @phpstan-ignore-next-line
        $surePayFailedCounter = $this->connection->get(self::SUREPAY_FAILED_COUNTER_KEY);

        $this->isHealthy = $surePayFailedCounter < 1;

        if ($surePayFailedCounter > 0) {
            $this->details = [
                'failed_count' => (int) $surePayFailedCounter,
            ];
        }
    }

    public static function increaseSurePayFailedCounter(Connection $connection): void
    {
        // @phpstan-ignore-next-line
        $connection->transaction(static function (Redis $redis) {
            $expire = self::SUREPAY_FAIL_EXPIRY_MINUTES * 60;

            $redis->incr(self::SUREPAY_FAILED_COUNTER_KEY, 1);
            $redis->expire(self::SUREPAY_FAILED_COUNTER_KEY, $expire);
        });
    }
}
