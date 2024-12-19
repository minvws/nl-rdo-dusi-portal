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

    public function isHookActive(ApplicationStage $applicationStage): bool
    {
        return $applicationStage->subsidyStage->subsidyVersion->id === self::SUBSIDY_AIGT_V1_UUID;
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
        $overrideFieldValue = $calculatorService->getOverrideFieldValue($fieldParams, $fieldValues, $applicationStage);
        if ($overrideFieldValue !== null) {
            return new FieldValue($fieldValue->field, $overrideFieldValue);
        }

        // Get the source field value
        $value = $calculatorService->getReferencedFieldValue(
            $applicationStage->application,
            $fieldParams->deadlineSourceFieldReference
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
