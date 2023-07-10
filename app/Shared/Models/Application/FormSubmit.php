<?php
declare(strict_types=1);

namespace App\Shared\Models\Application;

readonly class FormSubmit
{
    public function __construct(
        public Identity $identity,
        public ApplicationMetadata $applicationMetadata,
        public string $encryptedData
    )
    {}
}
