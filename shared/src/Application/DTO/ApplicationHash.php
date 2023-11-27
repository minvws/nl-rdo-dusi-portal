<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

class ApplicationHash
{
    public function __construct(
        public readonly string $hash,
        public readonly int $count,
        public readonly string $applicationIds
    ) {
    }
}
