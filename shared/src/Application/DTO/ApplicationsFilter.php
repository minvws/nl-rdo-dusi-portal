<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTime;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @throws \Exception
 */
function createDateTimeOrNull(array $inputArray, mixed $key): ?DateTime
{
    if (array_key_exists($key, $inputArray)) {
        if ($inputArray[$key] instanceof DateTime) {
            return $inputArray[$key];
        } else {
            return new DateTime($inputArray[$key]);
        }
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
        public ?string $applicationTitle,
        public ?string $reference,
        public ?DateTime $dateFrom,
        public ?DateTime $dateTo,
        public ?DateTime $dateLastModifiedFrom,
        public ?DateTime $dateLastModifiedTo,
        public ?DateTime $dateFinalReviewDeadlineFrom,
        public ?DateTime $dateFinalReviewDeadlineTo,
        public ?array $status,
        public ?array $subsidy,
        public ?array $phase,
    ) {
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function fromArray(array $inputArray): ApplicationsFilter
    {
        return new ApplicationsFilter(
            $inputArray['application_title'] ?? null,
            $inputArray['reference'] ?? null,
            createDateTimeOrNull($inputArray, 'date_from'),
            createDateTimeOrNull($inputArray, 'date_to'),
            createDateTimeOrNull($inputArray, 'date_last_modified_from'),
            createDateTimeOrNull($inputArray, 'date_last_modified_to'),
            createDateTimeOrNull($inputArray, 'date_final_review_deadline_from'),
            createDateTimeOrNull($inputArray, 'date_final_review_deadline_to'),
            getStatusOrNull($inputArray, 'status'),
            $inputArray['subsidy'] ?? null,
            $inputArray['phase'] ?? null,
        );
    }
}
