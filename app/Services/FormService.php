<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\FormKeyHelper;
use App\Repositories\FormCacheRepository;

readonly class FormService
{
    public function __construct(
        private FormCacheRepository $formCacheRepository,
        private FormKeyHelper       $formKeyHelper
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
}
