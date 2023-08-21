<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Models\SubsidyStageData;
use MinVWS\DUSi\Application\API\Services\Exceptions\SubsidyStageNotFoundException;
use Illuminate\Support\Facades\Log;

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
        Log::info('subsidyStage', [$subsidyStage]);
        if ($subsidyStage === null) {
            throw new SubsidyStageNotFoundException();
        }
        return $subsidyStage;
    }
}
