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
            $json = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if (is_string($json) === false) {
                throw new \Exception('Could not encode empty array to JSON');
            }
            return $json;
        }

        return $subsidies;
    }
}
