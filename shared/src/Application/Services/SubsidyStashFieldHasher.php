<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use LogicException;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHashField;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

/**
 * A SubsidyStageHash on multiple fields will be sorted based on the field->id to obtain the order of concattenating
 * field values. When a field value is missing an empty string is used. This hasher should not be triggered if
 * all field values are empty.
 */
class SubsidyStashFieldHasher
{
    public function makeApplicationFieldHash(
        SubsidyStageHash $subsidyStageHash,
        array $fieldValues,
        ApplicationStage $applicationStage
    ): string {
        $fieldValueCollection = collect($fieldValues);
        $fieldValueSearch = $fieldValueCollection->map(fn(FieldValue $fieldValue) =>  $fieldValue->valueToString());

        $concattedValues = $subsidyStageHash->subsidyStageHashFields()
            ->orderBy('field_id')
            ->get()
            ->map(
                function (SubsidyStageHashField $subsidyStageHashField) use ($fieldValueSearch): string {

                    return (string) ($subsidyStageHashField->field ?
                        $fieldValueSearch->get($subsidyStageHashField->field->code) :
                        '');
                }
            )->reduce(function (null|string $carry, string $value) {
                return $carry . $value;
            });

        if (empty($concattedValues)) {
            throw new LogicException("All fields in hash are empty. Hash will not be created.");
        }

        return $this->hash($concattedValues, $applicationStage);
    }

    public function hash(string $value, ApplicationStage $applicationStage): string
    {
        return hash_hmac('sha256', $value, $applicationStage->subsidyStage->id);
    }
}
