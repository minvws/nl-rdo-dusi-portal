<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Support\Facades\Redis as RedisFacade;
use Redis;

class SurepayServiceHealth extends AbstractServiceHealth
{
    private const SUREPAY_FAILED_COUNTER_KEY = 'surepay_failed_counter';
    private const SUREPAY_FAIL_EXPIRY_MINUTES = 15;

    protected function checkHealth(): void
    {
        $surePayFailedCounter = RedisFacade::get(self::SUREPAY_FAILED_COUNTER_KEY);

        $this->isHealthy = $surePayFailedCounter < 1;

        if ($surePayFailedCounter > 0) {
            $this->details = [
                'failed_count' => (int) $surePayFailedCounter,
            ];
        }
    }

    public static function updateSurePayFailedCounter(): void
    {
        RedisFacade::transaction(static function (Redis $redis) {
            $expire = self::SUREPAY_FAIL_EXPIRY_MINUTES * 60;

            $redis->incr(self::SUREPAY_FAILED_COUNTER_KEY, 1);
            $redis->expire(self::SUREPAY_FAILED_COUNTER_KEY, $expire);
        });
    }
}
