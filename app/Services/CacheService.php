<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\CacheKeyHelper;
use App\Http\Resources\FormResource;
use App\Http\Resources\SubsidyResource;
use App\Repositories\CacheRepository;
use App\Shared\Models\Definition\Form;
use Illuminate\Support\Collection;

readonly class CacheService
{
    public function __construct(
        private CacheRepository $cacheRepository,
        private CacheKeyHelper  $cacheKeyHelper
    ) {
    }

    public function cacheActiveSubsidies(Collection $subsidies): string|false
    {
        $key = $this->cacheKeyHelper->keyForActiveSubsidies();
        $json = SubsidyResource::collection($subsidies)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->cacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function cacheForm(Form $form): string|false
    {
        $key = $this->cacheKeyHelper->keyForForm($form);
        $resource = new FormResource($form);
        $json = $resource->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->cacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function purge(string $key): bool
    {
        return $this->cacheRepository->purge($key);
    }
}
