<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case RequestForChanges = 'requestForChanges';

    public function isEditableForApplicant(): bool
    {
        return in_array($this, [ApplicationStatus::Draft, ApplicationStatus::RequestForChanges], true);
    }

    public function isEditableForApplicantAfterClosure(): bool
    {
        return $this === ApplicationStatus::RequestForChanges;
    }

    public function isNewApplicationAllowed(): bool
    {
        return $this === ApplicationStatus::Rejected;
    }
}
