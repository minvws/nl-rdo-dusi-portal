<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Enums;

enum ApplicationStageVersionStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Invalid = 'invalid';
}
