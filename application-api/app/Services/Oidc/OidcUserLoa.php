<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services\Oidc;

enum OidcUserLoa: string
{
    case BASIC = 'http://eid.logius.nl/LoA/basic';
    case LOW = 'http://eidas.europa.eu/LoA/low';
    case SUBSTANTIAL = 'http://eidas.europa.eu/LoA/substantial';
    case HIGH = 'http://eidas.europa.eu/LoA/high';

    public static function isEqualOrHigher(self $minimumLoa, self $loa): bool
    {
        return match ($minimumLoa) {
            self::BASIC =>
                $loa === self::BASIC
                || $loa === self::LOW
                || $loa === self::SUBSTANTIAL
                || $loa === self::HIGH,
            self::LOW =>
                $loa === self::LOW
                || $loa === self::SUBSTANTIAL
                || $loa === self::HIGH,
            self::SUBSTANTIAL =>
                $loa === self::SUBSTANTIAL
                || $loa === self::HIGH,
            self::HIGH =>
                $loa === self::HIGH
        };
    }
}
