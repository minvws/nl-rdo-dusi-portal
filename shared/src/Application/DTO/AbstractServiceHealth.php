<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use JsonSerializable;
use MinVWS\DUSi\Shared\Application\Interfaces\ServiceHealth;

abstract class AbstractServiceHealth implements JsonSerializable, ServiceHealth
{
    protected bool $isHealthy = false;
    protected array $details = [];

    public function __construct(
        public readonly string $name,
    ) {
        $this->checkHealth();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isHealthy(): bool
    {
        return $this->isHealthy;
    }

    public function getDetails(): ?array
    {
        return $this->details ?? null;
    }

    abstract protected function checkHealth(): void;

    public function jsonSerialize(): ?array
    {
        $status = [
            'service' => $this->name,
            'isHealthy' => $this->isHealthy,
        ];

        if ($this->getDetails()) {
            $status['details'] = $this->getDetails();
        }

        return $status;
    }
}
