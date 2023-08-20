<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models;

use MinVWS\DUSi\Shared\Application\Shared\Models\Application\ApplicationMetadata;

class DraftFile
{
    public function __construct(
        public string $id,
        public ?string $extension,
        public ApplicationMetadata $applicationMetadata,
        public string $fieldCode
    ) {
    }
}
