<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition\Enums;

enum SubjectRole: string
{
    case Applicant = 'applicant';
    case Assessor = 'assessor';

    const Draft = 'draft';

    public static function getValues(): array
    {
        return array_column(SubjectRole::cases(), 'value');
    }

    public static function getDefault(): string
    {
        return SubjectRole::Draft;
    }
}
