<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Enums;

enum ReviewDeadlineSource: string
{
    case Field = 'field';
    case ExistingDeadline = 'existing_deadline';
    case Now = 'now';
}
