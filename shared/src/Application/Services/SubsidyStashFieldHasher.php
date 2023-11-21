<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHashField;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

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

        return $this->hash($concattedValues, $applicationStage);
    }

    public function hash(string $value, ApplicationStage $applicationStage): string
    {
        return hash_hmac('sha256', $value, $applicationStage->subsidyStage->id);
    }
}
