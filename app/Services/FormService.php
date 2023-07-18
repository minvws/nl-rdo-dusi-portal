<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\FormData;
use App\Services\Exceptions\FormNotFoundException;

readonly class FormService
{
    public function __construct(
        private CacheService $cacheService
    ) {
    }

    /**
     * @throws FormNotFoundException
     */
    public function getForm(string $id): FormData
    {
        $form = $this->cacheService->getCachedForm($id);
        if ($form === null) {
            throw new FormNotFoundException();
        }
        return $form;
    }
}
