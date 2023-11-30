<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Enums;

enum DataRetentionPeriod: string
{
    case Short = 'short';
    case Long = 'long';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getDefault(): DataRetentionPeriod
    {
        return self::Short;
    }
}
