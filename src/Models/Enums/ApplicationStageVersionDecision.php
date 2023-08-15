<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Enums;

enum ApplicationStageVersionDecision: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case RequestForChanges = 'request_for_changes';
}
