<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

enum Operator: string
{
    case Equal = '==';
    case Identical = '===';
    case NotEqual = '!=';
    case NotIdentical = '!==';
    case GreaterThan = '>';
    case GreaterThanOrEqualTo = '>=';
    case LessThan = '<';
    case LessThanOrEqualTo = '<=';
}
