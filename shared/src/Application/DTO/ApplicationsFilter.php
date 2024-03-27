<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTime;
use Exception;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;

/**
 * @throws Exception
 */
function createDateTimeOrNull(array $inputArray, mixed $key, bool $setTimeToEndOfDay = false): ?DateTime
{
    if (array_key_exists($key, $inputArray)) {
        $value = $inputArray[$key];

        if ($inputArray[$key] instanceof DateTime) {
            $value = $inputArray[$key];
        } else {
            $value = new DateTime($inputArray[$key]);
        }

        if ($setTimeToEndOfDay) {
            $value->setTime(23, 59, 59);
        }

        return $value;
    }
    return null;
}

/**
 * @return array<array-key, ApplicationStatus|null>|null
 */
function getStatusOrNull(array $inputArray, mixed $key): ?array
{
    if (!array_key_exists($key, $inputArray)) {
        return null;
    }

    $states = [];

    $stateList = $inputArray[$key];

    foreach ($stateList as $state) {
        if ($state instanceof ApplicationStatus) {
            $states[] = $state;
        } else {
            $states[] = ApplicationStatus::tryFrom($state);
        }
    }

    return $states;
}

class ApplicationsFilter
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public ?string $applicationTitle = null,
        public ?string $reference = null,
        public ?DateTime $dateFrom = null,
        public ?DateTime $dateTo = null,
        public ?DateTime $dateLastModifiedFrom = null,
        public ?DateTime $dateLastModifiedTo = null,
        public ?DateTime $dateFinalReviewDeadlineFrom = null,
        public ?DateTime $dateFinalReviewDeadlineTo = null,
        public ?array $status = null,
        public ?array $subsidy = null,
        public ?array $phase = null,
    ) {
    }

    /**
     * @throws Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function fromArray(array $inputArray): ApplicationsFilter
    {
        return new ApplicationsFilter(
            $inputArray['application_title'] ?? null,
            $inputArray['reference'] ?? null,
            createDateTimeOrNull($inputArray, 'date_from'),
            createDateTimeOrNull($inputArray, 'date_to', true),
            createDateTimeOrNull($inputArray, 'date_last_modified_from'),
            createDateTimeOrNull($inputArray, 'date_last_modified_to', true),
            createDateTimeOrNull($inputArray, 'date_final_review_deadline_from'),
            createDateTimeOrNull($inputArray, 'date_final_review_deadline_to', true),
            getStatusOrNull($inputArray, 'status'),
            $inputArray['subsidy'] ?? null,
            $inputArray['phase'] ?? null,
        );
    }
}
