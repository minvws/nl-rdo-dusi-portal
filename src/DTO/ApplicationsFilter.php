<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTime;
use InvalidArgumentException;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;

class ApplicationsFilter
{
    public array $validatedData;

    public function __construct(array $validatedData)
    {
        $this->checkType($validatedData);
        $this->validatedData = $validatedData;
    }

    private function checkType(array $validatedData): void
    {
        $keysToValidate = [
            'application_title',
            'date_from',
            'date_to',
            'status',
            'subsidy',
            'date_last_modified_from',
            'date_last_modified_to',
            'date_final_review_deadline_from',
            'date_final_review_deadline_to',
        ];

        foreach ($keysToValidate as $key) {
            if (isset($validatedData[$key])) {
                $this->validateData($key, $validatedData[$key]);
            }
        }
    }

    private function validateData(mixed $key, mixed $value): ApplicationStageVersionStatus|DateTime|string
    {
        return match ($key) {
            'application_title', 'subsidy' => $this->validateString($value),
            'date_from', 'date_to', 'date_last_modified_from', 'date_last_modified_to',
            'date_final_review_deadline_from',
            'date_final_review_deadline_to' => $this->validateDateTime($value),
            'status' => $this->validateStatus($value),
            default => throw new InvalidArgumentException("Unsupported key: $key"),
        };
    }

    private function validateString(mixed $value): string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("Invalid data type. Expected string.");
        }
        return $value;
    }

    private function validateDateTime(mixed $value): DateTime
    {
        if (!$value instanceof DateTime) {
            throw new InvalidArgumentException("Invalid data type. Expected DateTime.");
        }
        return $value;
    }

    private function validateStatus(mixed $value): ApplicationStageVersionStatus
    {
        if (!$value instanceof ApplicationStageVersionStatus) {
            throw new InvalidArgumentException("Invalid data type. Expected ApplicationStageVersionStatus.");
        }
        return $value;
    }
}
