<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\FormKeyHelper;
use App\Http\Resources\FormResource;
use App\Http\Resources\FormSchema;
use App\Models\Form;
use App\Repositories\FormCacheRepository;
use Illuminate\Support\Collection;

readonly class FormCacheService
{
    public function __construct(
        private FormCacheRepository $formCacheRepository,
        private FormKeyHelper       $formKeyHelper
    ) {
    }

    public function getKeys(): array
    {
        return $this->formCacheRepository->getKeys();
    }

    public function cacheFormList(Collection $forms): string|false
    {
        $key = $this->formKeyHelper->keyForList();
        $json = FormResource::collection($forms)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->formCacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function cacheForm(Form $form): string|false
    {
        $key = $this->formKeyHelper->keyForForm($form);
        $resource = new FormSchema($form);
        $json = $resource->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = $this->formCacheRepository->store($key, $json);
        return $result ? $key : false;
    }

    public function purge(string $key): bool
    {
        return $this->formCacheRepository->purge($key);
    }
}
