<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Support\Collection;

class ApplicationHash
{
    public function __construct(
        public readonly string $hash,
        public readonly int $count,
        public readonly Collection $applications
    ) {
    }
}
