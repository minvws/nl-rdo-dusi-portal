<?php
declare(strict_types=1);

namespace App\Models\Enums;

enum VersionStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
