<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

class TotalDistance implements FieldHook
{
    private const SUBSIDY_DAMU_UUID = '7b9f1318-4c38-4fe5-881b-074729d95abf';

    public function isHookActive(ApplicationStage $applicationStage): bool
    {
        return $applicationStage->subsidyStage->subsidyVersion->subsidy_id === self::SUBSIDY_DAMU_UUID;
    }

    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue
    {
        $totalDistance = TotalDistanceCalculator::calculate($fieldValues['travelDistanceSingleTrip']);

        return new FieldValue($fieldValue->field, $totalDistance);
    }
}
