<?php

declare(strict_types=1);

namespace App\Models\Enums;

enum VersionStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public static function getValues(): array
    {
        return array_column(VersionStatus::cases(), 'value');
    }

    public static function getDefault(): VersionStatus
    {
        return VersionStatus::Draft;
    }
}
