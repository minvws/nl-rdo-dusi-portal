<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Carbon\Carbon;
use DateInterval;
use DateMalformedIntervalStringException;
use DateTimeInterface;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Subsidy\Models\AssignationDeadlineFieldParams;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\ReviewDeadlineSource;
use MinVWS\DUSi\Shared\Subsidy\Models\FieldReference;

/**
 * Service to calculate the assignation deadline based on the field params.
 */
readonly class ReviewDeadlineCalculatorService
{
    public function __construct(
        private ApplicationDataService $applicationDataService,
    ) {
    }

    /**
     * Get the value of the override field if set
     *
     * @param AssignationDeadlineFieldParams $fieldParams
     * @param FieldValue[] $fieldValues
     * @param ApplicationStage $applicationStage
     * @return string|null The value of the override field or null if not set
     */
    public function getOverrideFieldValue(
        AssignationDeadlineFieldParams $fieldParams,
        array $fieldValues,
        ApplicationStage $applicationStage
    ): ?string {
        $fieldReference = $fieldParams->deadlineOverrideFieldReference;
        if ($fieldReference === null) {
            return null;
        }

        if ($fieldReference->stage !== $applicationStage->subsidyStage->stage) {
            Log::error('Invalid stage in deadlineOverrideFieldReference parameter');
            return null;
        }

        $value = $fieldValues[$fieldReference->fieldCode] ?? null;
        if ($value === null || empty($value->value) || !is_string($value->value)) {
            return null;
        }

        return $value->value;
    }

    /**
     * Get the ISO 8601 duration format from the params.
     *
     * @param AssignationDeadlineFieldParams $fieldParams
     * @return DateInterval|null The additional period or null if not set
     * @throws DateMalformedIntervalStringException
     */
    public function getAdditionalPeriod(AssignationDeadlineFieldParams $fieldParams): ?DateInterval
    {
        $additionalPeriod = $fieldParams->deadlineAdditionalPeriod;
        if (empty($additionalPeriod)) {
            return null;
        }

        return new DateInterval($additionalPeriod);
    }

    public function getReferencedFieldValue(Application $application, ?FieldReference $reference = null): ?Carbon
    {
        if ($reference === null) {
            return null;
        }

        $value = $this->applicationDataService->getApplicationStageDataForFieldByFieldReference(
            application: $application,
            field: $reference,
        );

        if (!is_string($value)) {
            return null;
        }

        $date = Carbon::createFromFormat("Y-m-d", $value);
        if ($date === false) {
            return null;
        }

        return $date;
    }

    public function getSourceFieldValue(
        Application $application,
        ReviewDeadlineSource $source,
        ?FieldReference $sourceFieldReference,
    ): ?Carbon {
        return match ($source) {
            ReviewDeadlineSource::Now => Carbon::now(),
            ReviewDeadlineSource::ExistingDeadline => $this->getCarbonInstance($application->final_review_deadline),
            ReviewDeadlineSource::Field => $this->getReferencedFieldValue($application, $sourceFieldReference),
            ReviewDeadlineSource::ApplicationSubmittedAt => $this->getCarbonInstance($application->submitted_at),
        };
    }

    protected function getCarbonInstance(?DateTimeInterface $date): ?Carbon
    {
        if ($date === null) {
            return null;
        }

        return Carbon::instance($date);
    }
}
