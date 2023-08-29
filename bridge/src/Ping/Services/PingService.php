<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Ping\Services;

use DateTimeImmutable;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Ping;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Pong;

class PingService
{
    public function ping(Ping $ping): Pong
    {
        return new Pong($ping->requestStamp, new DateTimeImmutable());
    }
}
