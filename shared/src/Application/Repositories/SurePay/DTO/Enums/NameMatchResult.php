<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums;

enum NameMatchResult: string
{
    // when the provided name matches the value of the account holder name held by the source.
    case Match = 'MATCH';
    // when the provided name closely resembles the value of the account holder name held by the source.
    case CloseMatch = 'CLOSE_MATCH';
    // when the provided name does not match the value of the account holder name held by the source.
    case NoMatch = 'NO_MATCH';
    // when the provided name could not be matched against the source data. This could have several reasons.
    case CouldNotMatch = 'COULD_NOT_MATCH';
    // when the provided name is too short
    case NameTooShort = 'NAME_TOO_SHORT';
}
