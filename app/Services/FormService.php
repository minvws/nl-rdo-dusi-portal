<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\CacheKeyHelper;
use App\Jobs\ProcessFileUpload;
use App\Jobs\ProcessFormSubmit;
use App\Repositories\CacheRepository;

class FormService
{
    public function __construct(
        private readonly CacheRepository $cacheRepository,
        private readonly CacheKeyHelper  $cacheKeyHelper
    ) {
    }

    public function getForm(string $id): ?string
    {
        return $this->cacheRepository->get($this->cacheKeyHelper->keyForFormId($id));
    }

    public function submitForm(string $id, string $data): void
    {
        ProcessFormSubmit::dispatch($id, $data);
    }

    public function uploadFile(string $formId, string $fileId, string $data): void
    {
        ProcessFileUpload::dispatch($formId, $fileId, $data);
    }
}
