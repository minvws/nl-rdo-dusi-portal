<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

class TravelExpenseReimbursement implements FieldHook
{
    private const SUBSIDY_DAMU_UUID = '7b9f1318-4c38-4fe5-881b-074729d95abf';

    public function isHookActive(ApplicationStage $applicationStage): bool
    {
        return $applicationStage->subsidyStage->subsidyVersion->subsidy_id === self::SUBSIDY_DAMU_UUID;
    }

    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue
    {
        if ($fieldValues['educationType']->value === null || $fieldValues['travelDistanceSingleTrip']->value === null) {
            return new FieldValue($fieldValue->field, null);
        }

        if ($fieldValues['educationType']->value === EducationalType::PRIMARY_EDUCATION) {
            return new FieldValue(
                $fieldValue->field,
                round(
                    TravelExpenseReimbursementCalculator::calculateForPrimaryEducation(
                        AnnualJointIncomeCalculator::calculate($fieldValues)
                    ),
                    2
                )
            );
        }

        if ($fieldValues['educationType']->value === EducationalType::SECONDARY_EDUCATION) {
            return new FieldValue(
                $fieldValue->field,
                round(
                    TravelExpenseReimbursementCalculator::calculateForSecondaryEducation(
                        AnnualJointIncomeCalculator::calculate($fieldValues)
                    ),
                    2
                )
            );
        }

        return new FieldValue($fieldValue->field, null);
    }
}
