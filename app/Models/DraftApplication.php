<?php

declare(strict_types=1);

namespace App\Models;


use MinVWS\DUSi\Shared\Application\Shared\Models\Application\ApplicationMetadata;

class DraftApplication implements Application
{
    public function __construct(
        public string $id,
        public string $formId
    ) {
    }

    public function getMetadata(): ApplicationMetadata
    {
        return new ApplicationMetadata($this->id, $this->formId);
    }
}
