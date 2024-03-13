<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

class AnnualJointIncomeCalculator
{
    public static function calculate(array $fieldValues): int
    {
        return self::getAlimonyAmount($fieldValues) +
            self::getannualIncomeParentA($fieldValues) +
            self::getannualIncomeParentB($fieldValues);
    }

    private static function getannualIncomeParentA(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['annualIncomeParentA']->value) ?
            $fieldValues['annualIncomeParentA']->value : 0);
    }

    private static function getannualIncomeParentB(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['annualIncomeParentB']->value) ?
            $fieldValues['annualIncomeParentB']->value : 0);
    }

    private static function getAlimonyAmount(array $fieldValues): int
    {
        return (int)($fieldValues['hasAlimony']->value === 'Ja' && is_numeric($fieldValues['alimonyAmount']->value) ?
            $fieldValues['alimonyAmount']->value : 0);
    }
}
