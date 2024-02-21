<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

class YearlyIncomeTotal implements FieldHook
{
    private const SUBSIDY_DAMU_UUID = '7b9f1318-4c38-4fe5-881b-074729d95abf';

    public function isHookActive(ApplicationStage $applicationStage): bool
    {
        return $applicationStage->subsidyStage->subsidyVersion->subsidy_id === self::SUBSIDY_DAMU_UUID;
    }

    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue
    {
        $yearlyIncomeTotal =
            $this->getAlimonyAmount($fieldValues) +
            $this->getYearlyIncomeParent1($fieldValues) +
            $this->getYearlyIncomeParent2($fieldValues);

        return new FieldValue($fieldValue->field, $yearlyIncomeTotal);
    }

    public function getAlimonyAmount(array $fieldValues): int
    {
        return (int)($fieldValues['hasAlimony']->value === 'Ja' && is_numeric($fieldValues['alimonyAmount']->value) ?
            $fieldValues['alimonyAmount']->value : 0);
    }

    public function getYearlyIncomeParent1(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['yearlyIncomeParent1']->value) ?
            $fieldValues['yearlyIncomeParent1']->value : 0);
    }

    public function getYearlyIncomeParent2(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['yearlyIncomeParent2']->value) ?
            $fieldValues['yearlyIncomeParent2']->value : 0);
    }
}
