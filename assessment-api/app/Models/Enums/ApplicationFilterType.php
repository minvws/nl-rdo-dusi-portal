<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Models\Enums;

enum ApplicationFilterType: string
{
    case Priority = 'PRIORITY';
    case Taken = 'TAKEN';
    case Assigned = 'ASSIGNED';
    case AssignedToMe = 'ASSIGNED_TO_ME';
}
