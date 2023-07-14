<?php
declare(strict_types=1);

namespace App\Models;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
}
