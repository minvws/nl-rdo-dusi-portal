<?php

declare(strict_types=1);

namespace App\Models\Enums;

enum ApplicationStageStatus: string
{
    case DRAFT = 'DRAFT';
    case Submitted = 'submitted';
}
