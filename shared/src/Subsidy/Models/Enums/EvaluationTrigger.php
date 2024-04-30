<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Enums;

enum EvaluationTrigger: string
{
    case Submit = 'submit';
    case Expiration = 'expiration';
}
