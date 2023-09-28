<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums;

enum AccountType: string
{
    // The bank account holder is a Natural Person
    case NaturalPerson = 'NP';
    // The bank account holder is an organisation
    case Organisation = 'ORG';
    // The bank account holder is an unknown to us
    case Unknown = 'UNKNOWN';
}
