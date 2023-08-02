<?php

declare(strict_types=1);

namespace App\Shared\Models\Application;

class Identity
{
    public function __construct(
        public IdentityType $type,
        public string $identifier
    ) {
    }
}
