<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\FormKeyHelper;
use App\Jobs\ProcessFormSubmit;
use App\Repositories\FormCacheRepository;

class FormService
{
    public function __construct(
        private readonly FormCacheRepository $formCacheRepository,
        private readonly FormKeyHelper $formKeyHelper
    ) {
    }

    public function getActiveForms(): string
    {
        $forms = $this->formCacheRepository->get($this->formKeyHelper->keyForList());

        if ($forms === null) {
            return json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return $forms;
    }

    public function getFormSchema(string $id): ?string
    {
        return $this->formCacheRepository->get($this->formKeyHelper->keyForFormId($id));
    }

    public function submitForm(string $id, string $data): void
    {
        ProcessFormSubmit::dispatch($id, $data);
    }
}
