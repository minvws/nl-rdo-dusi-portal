<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

class AnnualJointIncomeCalculator
{
    public static function calculate(array $fieldValues): int
    {
        return self::getAnnualIncomeParentA($fieldValues) +
            self::getAnnualIncomeParentB($fieldValues);
    }

    private static function getAnnualIncomeParentA(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['annualIncomeParentA']->value) ?
            $fieldValues['annualIncomeParentA']->value : 0);
    }

    private static function getAnnualIncomeParentB(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['annualIncomeParentB']->value) ?
            $fieldValues['annualIncomeParentB']->value : 0);
    }
}
