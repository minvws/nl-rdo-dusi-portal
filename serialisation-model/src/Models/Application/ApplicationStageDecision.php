<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum ApplicationStageDecision: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case RequestForChanges = 'requestForChanges';
}
