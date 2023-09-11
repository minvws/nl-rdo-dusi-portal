<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;

class DraftApplication implements Application
{
    public function __construct(
        public string $id,
        public string $formId
    ) {
    }

    public function getMetadata(): ApplicationMetadata
    {
        return new ApplicationMetadata($this->id, $this->formId, true);
    }
}
