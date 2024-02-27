<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

class AnnualJointIncomeCalculator
{
    public static function calculate(array $fieldValues): int
    {
        return self::getAlimonyAmount($fieldValues) +
            self::getAnnualIncomeParent1($fieldValues) +
            self::getAnnualIncomeParent2($fieldValues);
    }

    private static function getAnnualIncomeParent2(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['annualIncomeParent2']->value) ?
            $fieldValues['annualIncomeParent2']->value : 0);
    }

    private static function getAnnualIncomeParent1(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['annualIncomeParent1']->value) ?
            $fieldValues['annualIncomeParent1']->value : 0);
    }

    private static function getAlimonyAmount(array $fieldValues): int
    {
        return (int)($fieldValues['hasAlimony']->value === 'Ja' && is_numeric($fieldValues['alimonyAmount']->value) ?
            $fieldValues['alimonyAmount']->value : 0);
    }
}
