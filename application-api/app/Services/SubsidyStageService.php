<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Models\SubsidyStageData;
use MinVWS\DUSi\Application\API\Services\Exceptions\SubsidyStageNotFoundException;

class SubsidyStageService
{
    public function __construct(
        private CacheService $cacheService
    ) {
    }

    /**
     * @throws SubsidyStageNotFoundException
     */
    public function getSubsidyStageData(string $id): SubsidyStageData
    {
        $subsidyStage = $this->cacheService->getCachedSubsidyStageData($id);
        if ($subsidyStage === null) {
            throw new SubsidyStageNotFoundException();
        }
        return $subsidyStage;
    }
}
