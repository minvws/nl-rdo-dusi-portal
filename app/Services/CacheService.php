<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CacheKeyHelper;
use App\Http\Resources\SubsidyStageResource;
use App\Http\Resources\SubsidyResource;
use App\Models\SubsidyStageData;
use App\Repositories\CacheRepository;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;

class CacheService
{
    public function __construct(
        private CacheRepository $cacheRepository,
        private CacheKeyHelper $cacheKeyHelper
    ) {
    }

    public function cacheActiveSubsidies(Collection $subsidies): string|false
    {
        $key = $this->cacheKeyHelper->keyForActiveSubsidies();
        $json = SubsidyResource::collection($subsidies)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->cacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function getCachedSubsidyStage(string $subsidyStageId): ?SubsidyStageData
    {
        $json = $this->cacheRepository->get($this->cacheKeyHelper->keyForSubsidyStageId($subsidyStageId));
        return $json === null ? null : new SubsidyStageData($subsidyStageId, $json);
    }

    public function cacheSubsidyStage(SubsidyStage $subsidyStage): string|false
    {
        $key = $this->cacheKeyHelper->keyForSubsidyStage($subsidyStage);
        $resource = new SubsidyStageResource($subsidyStage);
        $json = $resource->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->cacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function purge(string $key): bool
    {
        return $this->cacheRepository->purge($key);
    }
}
