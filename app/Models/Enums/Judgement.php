<?php
namespace App\Models\Enums;

enum Judgement: string
{
    case approved = 'approved';
    case rejected = 'rejected';
    case pending = 'pending';
}
