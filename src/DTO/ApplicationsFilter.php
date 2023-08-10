<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTime;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;

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

function getStatusOrNull(array $inputArray, mixed $key): ?ApplicationStageVersionStatus
{
    if (array_key_exists($key, $inputArray)) {
        if ($inputArray[$key] instanceof ApplicationStageVersionStatus) {
            return $inputArray[$key];
        } else {
            return ApplicationStageVersionStatus::tryFrom($inputArray[$key]);
        }
    }
    return null;
}

class ApplicationsFilter
{
    public function __construct(
        public ?string $applicationTitle,
        public ?DateTime $dateFrom,
        public ?DateTime $dateTo,
        public ?DateTime $dateLastModifiedFrom,
        public ?DateTime $dateLastModifiedTo,
        public ?DateTime $dateFinalReviewDeadlineFrom,
        public ?DateTime $dateFinalReviewDeadlineTo,
        public ?ApplicationStageVersionStatus $status,
        public ?string $subsidy,
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
            createDateTimeOrNull($inputArray, 'date_from'),
            createDateTimeOrNull($inputArray, 'date_to'),
            createDateTimeOrNull($inputArray, 'date_last_modified_from'),
            createDateTimeOrNull($inputArray, 'date_last_modified_to'),
            createDateTimeOrNull($inputArray, 'date_final_review_deadline_from'),
            createDateTimeOrNull($inputArray, 'date_final_review_deadline_to'),
            getStatusOrNull($inputArray, 'status'),
            $inputArray['subsidy'] ?? null,
        );
    }
}
