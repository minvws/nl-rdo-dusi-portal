<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums;

enum AccountNumberValidation: string
{
    // An account that conforms to the standards, e.g. a valid Mod97 calculation for an IBAN.
    case Valid = 'VALID';
    // Account is an account that does not conform to the standards.
    case Invalid = 'NOT_VALID';
}
