<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageData;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;

class ActualRequestedSubsidyAmount extends RequestedSubsidyAmount
{
    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue
    {
        $applicantEducationType = $this->getApplicantEducationType($applicationStage);

        if (
            $applicantEducationType === null ||
            $fieldValues['actualTravelDistanceSingleTrip']->value === null ||
            $fieldValues['actualAnnualJointIncome']->value === null
        ) {
            return new FieldValue($fieldValue->field, null);
        }

        if ($applicantEducationType === EducationType::PRIMARY_EDUCATION) {
            return new FieldValue(
                $fieldValue->field,
                round(TravelExpenseReimbursementCalculator::calculateForPrimaryEducation(
                    (int)$fieldValues['actualAnnualJointIncome']->value
                ) *
                    TotalDistanceCalculator::calculate($fieldValues['actualTravelDistanceSingleTrip']), 2)
            );
        }

        if ($applicantEducationType === EducationType::SECONDARY_EDUCATION) {
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

    public function getApplicantEducationType(ApplicationStage $currentApplicationStage): ?string
    {
        /** @var ApplicationDataService $applicationDataService */
        $applicationDataService = app(ApplicationDataService::class);

        /** @var ApplicationStageData $applicantApplicationStageData */
        $applicantApplicationStageData = $applicationDataService->getApplicantApplicationStageData(
            $currentApplicationStage->application
        );

        if (is_null($applicantApplicationStageData)) {
            return null;
        }

        /** @psalm-suppress UndefinedPropertyFetch */
        return $applicantApplicationStageData?->educationType; // @phpstan-ignore-line
    }
}
