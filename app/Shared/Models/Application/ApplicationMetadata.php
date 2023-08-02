<?php

declare(strict_types=1);

namespace App\Shared\Models\Application;

class ApplicationMetadata
{
    public function __construct(
        public string $applicationStageId,
        public string $subsidyStageId,
    ) {
    }
}
