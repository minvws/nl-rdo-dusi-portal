<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CacheKeyHelper;
use App\Repositories\CacheRepository;

class SubsidyService
{
    public function __construct(
        private readonly CacheRepository $cacheRepository,
        private readonly CacheKeyHelper $cacheKeyHelper
    ) {
    }

    public function getActiveSubsidies(): string
    {
        $subsidies = $this->cacheRepository->get($this->cacheKeyHelper->keyForActiveSubsidies());

        if ($subsidies === null) {
            return json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return $subsidies;
    }
}
