<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Helpers\CacheKeyHelper;
use MinVWS\DUSi\Application\API\Http\Resources\SubsidyStageResource;
use MinVWS\DUSi\Application\API\Http\Resources\SubsidyResource;
use MinVWS\DUSi\Application\API\Models\SubsidyStageData;
use MinVWS\DUSi\Application\API\Repositories\CacheRepository;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;

class CacheService
{
    public function __construct(
        private readonly CacheRepository $cacheRepository,
        private readonly CacheKeyHelper $cacheKeyHelper,
        private readonly SubsidyStageDataSchemaBuilder $dataSchemaBuilder
    ) {
    }

    public function cacheActiveSubsidies(Collection $subsidies): string|false
    {
        $key = $this->cacheKeyHelper->keyForActiveSubsidies();
        $json = SubsidyResource::collection($subsidies)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->cacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function getCachedSubsidyStageData(string $subsidyStageId): ?SubsidyStageData
    {
        $json = $this->cacheRepository->get($this->cacheKeyHelper->keyForSubsidyStageId($subsidyStageId));
        return $json === null ? null : new SubsidyStageData($subsidyStageId, $json);
    }

    public function cacheSubsidyStage(SubsidyStage $subsidyStage): string|false
    {
        $key = $this->cacheKeyHelper->keyForSubsidyStage($subsidyStage);
        $resource = new SubsidyStageResource($subsidyStage, $this->dataSchemaBuilder);
        $json = $resource->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->cacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function purge(string $key): bool
    {
        return $this->cacheRepository->purge($key);
    }
}
