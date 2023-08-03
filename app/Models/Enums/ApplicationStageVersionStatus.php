<?php

declare(strict_types=1);

namespace App\Models\Enums;

enum ApplicationStageVersionStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
}
