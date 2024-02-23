<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

class YearlyIncomeCalculator
{
    public static function calculate(array $fieldValues): int
    {
        return self::getAlimonyAmount($fieldValues) +
            self::getYearlyIncomeParent1($fieldValues) +
            self::getYearlyIncomeParent2($fieldValues);
    }

    private static function getYearlyIncomeParent2(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['yearlyIncomeParent2']->value) ?
            $fieldValues['yearlyIncomeParent2']->value : 0);
    }

    private static function getYearlyIncomeParent1(array $fieldValues): int
    {
        return (int)(is_numeric($fieldValues['yearlyIncomeParent1']->value) ?
            $fieldValues['yearlyIncomeParent1']->value : 0);
    }

    private static function getAlimonyAmount(array $fieldValues): int
    {
        return (int)($fieldValues['hasAlimony']->value === 'Ja' && is_numeric($fieldValues['alimonyAmount']->value) ?
            $fieldValues['alimonyAmount']->value : 0);
    }
}
