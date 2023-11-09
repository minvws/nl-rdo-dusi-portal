<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Enums;

enum ValidationResultType: string
{
    case Error = 'error';
    case Warning = 'warning';
    case Explanation = 'explanation';
    case Success = 'confirmation';
}
