<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SubsidyStageData;
use App\Services\Exceptions\SubsidyStageNotFoundException;

class SubsidyStageService
{
    public function __construct(
        private CacheService $cacheService
    ) {
    }

    /**
     * @throws SubsidyStageNotFoundException
     */
    public function getSubsidyStage(string $id): SubsidyStageData
    {
        $subsidyStage = $this->cacheService->getCachedSubsidyStage($id);
        if ($subsidyStage === null) {
            throw new SubsidyStageNotFoundException();
        }
        return $subsidyStage;
    }
}
