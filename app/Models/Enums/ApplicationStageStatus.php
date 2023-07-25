<?php

declare(strict_types=1);

namespace App\Models\Enums;

enum ApplicationStageStatus: string
{
    case Draft = 'Draft';
    case Submitted = 'submitted';
}
