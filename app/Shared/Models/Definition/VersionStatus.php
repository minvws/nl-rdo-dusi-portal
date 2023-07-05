<?php
declare(strict_types=1);

namespace App\Shared\Models\Definition;

enum VersionStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
