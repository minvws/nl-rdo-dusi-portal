<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTime;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;


/**
 * @throws \Exception
 */
function createDateTimeOrNull($inputArray, $key): ?DateTime
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

function getStatusOrNull($inputArray, $key): ?ApplicationStageVersionStatus
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
        $keys = [
            'application_title',
            'date_from',
            'date_to',
            'date_last_modified_from',
            'date_last_modified_to',
            'date_final_review_deadline_from',
            'date_final_review_deadline_to',
            'status',
            'subsidy'
        ];

        $values = [];
        foreach ($keys as $key) {
            if ($key === 'status') {
                $values[] = getStatusOrNull($inputArray, $key);
            } elseif (str_contains('date', $key)) {
                $values[] = createDateTimeOrNull($inputArray, $key);
            }
            else {
                $values[] = $inputArray[$key] ?? null;
            }
        }
        return new ApplicationsFilter(...$values);
    }
}
