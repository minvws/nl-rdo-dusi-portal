<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Enums;

enum ApplicationStageStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
}
