<?php
declare(strict_types=1);

namespace App\Models\Definition;

enum FormStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
