<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;

class SubmittedApplication extends DraftApplication
{
    public function getMetadata(): ApplicationMetadata
    {
        return new ApplicationMetadata($this->id, $this->formId, false);
    }

    public static function fromDraft(DraftApplication $draftApplication): SubmittedApplication
    {
        return new SubmittedApplication($draftApplication->id, $draftApplication->formId);
    }
}
