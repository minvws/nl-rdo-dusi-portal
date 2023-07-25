<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition\Enums;

enum SubjectRole: string
{
    case Applicant = 'applicant';
    case Assessor = 'assessor';

    public static function getValues(): array
    {
        return array_column(SubjectRole::cases(), 'value');
    }

    public static function getDefault(): SubjectRole
    {
        return SubjectRole::Applicant;
    }
}
