<?php
declare(strict_types=1);

namespace App\Models;

use App\Shared\Models\Application\ApplicationMetadata;

readonly class DraftFile
{
    public function __construct(
        public string $id,
        public ?string $extension,
        public ApplicationMetadata $applicationMetadata,
        public string $fieldCode
    ) {
    }
}
