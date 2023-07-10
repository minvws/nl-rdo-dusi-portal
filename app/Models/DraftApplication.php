<?php
declare(strict_types=1);

namespace App\Models;

use App\Shared\Models\Application\ApplicationMetadata;

readonly class DraftApplication implements Application
{
    public function __construct(
        public string $id,
        public string $formId
    )
    {}

    public function getMetadata(): ApplicationMetadata
    {
        return new ApplicationMetadata($this->id, $this->formId);
    }
}
