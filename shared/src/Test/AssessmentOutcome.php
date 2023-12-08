<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Test;

enum AssessmentOutcome: string
{
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CHANGES_REQUESTED = 'changes_requested';
    case AGREES = 'agrees';
    case DISAGREES = 'disagrees';
    case UNASSESSED = "unassessed";
    case SUPPLEMENT_NEEDED = 'supplement_needed';
}
