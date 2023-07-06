<?php

declare(strict_types=1);

namespace App\Models\Enums;

enum Judgement: string
{
    case approved = 'approved';
    case rejected = 'rejected';
    case pending = 'pending';
}
