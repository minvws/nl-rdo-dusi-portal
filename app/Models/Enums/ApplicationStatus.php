<?php
declare(strict_types=1);

namespace App\Models\Enums;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
}
