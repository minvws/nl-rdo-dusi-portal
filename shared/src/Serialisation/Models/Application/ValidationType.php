<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum ValidationParam: string
{
    case Submit = 'submit';
    case ProvidedFields = 'validate';
    case Wit = 'approvedk';
    case Rejected = 'rejected';
    case RequestForChanges = 'requestForChanges';

    public function isEditableForApplicant(): bool
    {
        return in_array($this, [ApplicationStatus::Draft, ApplicationStatus::RequestForChanges], true);
    }

    public function isNewApplicationAllowed(): bool
    {
        return $this === ApplicationStatus::Rejected;
    }
}
