<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums;

enum AccountStatus: string
{
    // account is a valid account and supported for checks.
    case Active = 'ACTIVE';
    // account is a valid account marked by the account holding bank as inactive.
    case Inactive = 'INACTIVE';
    //  account status stands for an account that is valid but is not supported to perform any checks.
    case NotSupported = 'NOT_SUPPORTED';
    //  account status stands for an account that is valid but could not be found in any of the connected
    case NotFound = 'NOT_FOUND';
    // account status is for an account that is either found as part of DERIVED data or a NOT_VALID account.
    case Unknown = 'UNKNOWN';
}
