<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Interfaces;

interface ServiceHealth
{
    public function getName(): string;
    public function isHealthy(): bool;

    /**
     * @return array<mixed>|null
     */
    public function getDetails(): ?array;
}
