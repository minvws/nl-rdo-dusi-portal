<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Shared\Models\Application;

readonly class FormSubmit
{
    public function __construct(
        public Identity $identity,
        public ApplicationMetadata $applicationMetadata,
        public string $encryptedData
    ) {
    }
}
