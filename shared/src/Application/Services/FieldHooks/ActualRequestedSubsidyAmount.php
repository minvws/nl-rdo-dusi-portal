<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

class ActualRequestedSubsidyAmount extends RequestedSubsidyAmount
{
    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue
    {
        if (
            $fieldValues['actualEducationType']->value === null ||
            $fieldValues['actualTravelDistanceSingleTrip']->value === '' ||
            $fieldValues['actualAnnualJointIncome']->value === ''
        ) {
            return new FieldValue($fieldValue->field, null);
        }

        if ($fieldValues['actualEducationType']->value === 'Primair onderwijs') {
            return new FieldValue(
                $fieldValue->field,
                round(TravelExpenseReimbursementCalculator::calculateForPrimaryEducation(
                    (int)$fieldValues['actualAnnualJointIncome']->value
                ) *
                    TotalDistanceCalculator::calculate($fieldValues['actualTravelDistanceSingleTrip']), 2)
            );
        }

        if ($fieldValues['actualEducationType']->value === 'Voortgezet onderwijs') {
            return new FieldValue(
                $fieldValue->field,
                round(TravelExpenseReimbursementCalculator::calculateForSecondaryEducation(
                    (int)$fieldValues['actualAnnualJointIncome']->value
                ) *
                    TotalDistanceCalculator::calculate($fieldValues['actualTravelDistanceSingleTrip']), 2)
            );
        }

        return new FieldValue($fieldValue->field, null);
    }
}
