<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Allocated = 'allocated';
    case Rejected = 'rejected';
    case Reclaimed = 'reclaimed';

    case RequestForChanges = 'requestForChanges';

    public const NEW_APPLICATION_ALLOWED_STATUSES = [self::Rejected, self::Reclaimed];
    public const EDIT_ALLOWED_STATUSES = [self::Draft, self::RequestForChanges];
    public const EDIT_AFTER_CLOSURE_ALLOWED_STATUSES = [self::RequestForChanges];

    public function isEditableForApplicant(): bool
    {
        return in_array($this, self::EDIT_ALLOWED_STATUSES, true);
    }

    public function isEditableForApplicantAfterClosure(): bool
    {
        return in_array($this, self::EDIT_AFTER_CLOSURE_ALLOWED_STATUSES, true);
    }

    public function isNewApplicationAllowed(): bool
    {
        return in_array($this, self::NEW_APPLICATION_ALLOWED_STATUSES, true);
    }

    /**
     * Function to get the difference between two arrays of ApplicationStatuses
     * array_diff does not work with enums, so we need to use array_udiff.
     *
     * @param ApplicationStatus[] $array
     * @param ApplicationStatus[] $array2
     * @return ApplicationStatus[]
     */
    public static function getDiff(array $array, array $array2): array
    {
        return array_values(array_udiff(
            $array,
            $array2,
            static fn(self $valueA, self $valueB) => $valueA->value <=> $valueB->value,
        ));
    }
}
