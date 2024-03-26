<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationReference;

class ApplicationReferenceRepository
{
    public function isReferenceUnique(string $applicationReference): bool
    {
        return ApplicationReference::where('reference', $applicationReference)->count() === 0;
    }

    public function saveReference(string $applicationReference): void
    {
        ApplicationReference::create(
            [
                'reference' => $applicationReference,
                'used' => true,
                'deleted' => false,
            ]
        );
    }

    public function setReferenceToDeleted(string $applicationReference): void
    {
        ApplicationReference::where('reference', $applicationReference)
            ->update(['deleted' => true])
        ;
    }
}
