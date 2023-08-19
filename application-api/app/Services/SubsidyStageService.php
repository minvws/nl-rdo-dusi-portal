<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SubsidyStageData;
use App\Services\Exceptions\SubsidyStageNotFoundException;
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
