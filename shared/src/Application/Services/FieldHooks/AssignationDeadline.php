<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use DateMalformedIntervalStringException;
use JsonException;
use MinVWS\Codable\Decoding\Decoder;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Services\AssignationDeadlineCalculatorService;
use MinVWS\DUSi\Shared\Subsidy\Models\AssignationDeadlineFieldParams;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class AssignationDeadline implements FieldHook
{
    private const SUBSIDY_AIGT_V1_UUID = '2aaac0da-d265-40bb-bde6-ac20d77e6bca';
    private const SUBSIDY_BTV_V1_UUID = '907bb399-0d19-4e1a-ac75-25a864df27c6';

    public function isHookActive(ApplicationStage $applicationStage): bool
    {
        return $applicationStage->subsidyStage->subsidyVersion->id === self::SUBSIDY_AIGT_V1_UUID
            || $applicationStage->subsidyStage->subsidyVersion->id === self::SUBSIDY_BTV_V1_UUID;
    }

    /**
     * @throws DateMalformedIntervalStringException
     * @throws JsonException
     */
    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue
    {
        $calculatorService = $this->calculatorService();
        $fieldParams = $this->getFieldParams($fieldValue->field);

        // Use the override field value if it is set
        $overrideFieldValue = $calculatorService->getOverrideFieldValue(
            fieldParams: $fieldParams,
            fieldValues: $fieldValues,
            applicationStage: $applicationStage,
        );
        if ($overrideFieldValue !== null) {
            return new FieldValue($fieldValue->field, $overrideFieldValue);
        }

        if ($fieldParams->deadlineSource === null) {
            return new FieldValue($fieldValue->field, null);
        }

        // Get the source field value
        $value = $calculatorService->getSourceFieldValue(
            application: $applicationStage->application,
            source: $fieldParams->deadlineSource,
            sourceFieldReference: $fieldParams->deadlineSourceFieldReference,
        );
        if ($value === null) {
            return new FieldValue($fieldValue->field, null);
        }

        // Add the additional period if it is set
        $additionalPeriod = $calculatorService->getAdditionalPeriod($fieldParams);
        if ($additionalPeriod !== null) {
            $value->add($additionalPeriod);
        }

        return new FieldValue($fieldValue->field, $value->toDateString());
    }

    private function calculatorService(): AssignationDeadlineCalculatorService
    {
        return app(AssignationDeadlineCalculatorService::class);
    }

    private function getFieldParams(Field $field): AssignationDeadlineFieldParams
    {
        return (new Decoder())->decode($field->params)->decodeObject(AssignationDeadlineFieldParams::class);
    }
}
