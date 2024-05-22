<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Database\Eloquent\Collection;
use MinVWS\DUSi\Shared\Application\Models\Application;

readonly class ApplicationHash
{
    /**
     * @param string $hash
     * @param int $count
     * @param Collection<int, Application> $applications
     */
    public function __construct(
        public string $hash,
        public int $count,
        public Collection $applications
    ) {
    }
}
