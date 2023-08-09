<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTime;
use Illuminate\Support\Facades\Date;
use InvalidArgumentException;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;

use function PHPUnit\Framework\isInstanceOf;

class ApplicationsFilter
{
    public function __construct(
        public mixed $applicationTitle,
        public mixed $dateFrom,
        public mixed $dateTo,
        public mixed $dateLastModifiedFrom,
        public mixed $dateLastModifiedTo,
        public mixed $dateFinalReviewDeadlineFrom,
        public mixed $dateFinalReviewDeadlineTo,
        public mixed $status,
        public mixed $subsidy,
    ) {
    }

    /**
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function fromArray(array $inputArray): ApplicationsFilter
    {
        return new ApplicationsFilter(
            array_key_exists('application_title', $inputArray)
                ? $inputArray['application_title'] : null,
            array_key_exists('date_from', $inputArray)
                ? ($inputArray['date_from'] instanceof DateTime
                ? $inputArray['date_from'] : new DateTime($inputArray['date_from'])) : null,
            array_key_exists('date_to', $inputArray)
                ? ($inputArray['date_to'] instanceof DateTime
                ? $inputArray['date_to'] : new DateTime($inputArray['date_to'])) : null,
            array_key_exists('date_last_modified_from', $inputArray)
                ? ($inputArray['date_last_modified_from'] instanceof DateTime
                ? $inputArray['date_last_modified_from'] : new DateTime($inputArray['date_last_modified_from'])) : null,
            array_key_exists('date_last_modified_to', $inputArray)
                ? ($inputArray['date_last_modified_to'] instanceof DateTime
                ? $inputArray['date_last_modified_to'] : new DateTime($inputArray['date_last_modified_to'])) : null,
            array_key_exists('date_final_review_deadline_from', $inputArray)
                ? ($inputArray['date_final_review_deadline_from'] instanceof DateTime
                ? $inputArray['date_final_review_deadline_from'] :
                new DateTime($inputArray['date_final_review_deadline_from'])) : null,
            array_key_exists('date_final_review_deadline_to', $inputArray)
                ? ($inputArray['date_final_review_deadline_to'] instanceof DateTime
                ? $inputArray['date_final_review_deadline_to'] :
                new DateTime($inputArray['date_final_review_deadline_to'])) : null,
            array_key_exists('status', $inputArray)
            ? ($inputArray['status'] instanceof ApplicationStageVersionStatus
                ? $inputArray['status'] : ApplicationStageVersionStatus::tryFrom($inputArray['status'])) : null,
            array_key_exists('subsidy', $inputArray)
                ? $inputArray['subsidy'] : null,
        );
    }
}
