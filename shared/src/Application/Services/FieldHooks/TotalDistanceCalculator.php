<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

class TotalDistanceCalculator
{
    public const YEARLY_TRIPS_BOTH_WAYS = 378;

    public static function calculate(FieldValue $travelDistanceSingleTrip): float
    {
        if (!is_numeric($travelDistanceSingleTrip->value)) {
            return 0.00;
        }

        $totalDistance = (!empty($travelDistanceSingleTrip->value) ?
            $travelDistanceSingleTrip->value * self::YEARLY_TRIPS_BOTH_WAYS : 0);

        return (float)$totalDistance;
    }
}
