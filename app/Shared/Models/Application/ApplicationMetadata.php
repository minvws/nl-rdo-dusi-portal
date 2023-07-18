<?php
declare(strict_types=1);

namespace App\Shared\Models\Application;

readonly class ApplicationMetadata
{
    public function __construct(
        public string $id,
        public string $formId
    )
    {}
}
