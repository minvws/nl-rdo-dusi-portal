<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case RequestForChanges = 'requestForChanges';
    case Invalid = 'invalid';
}
