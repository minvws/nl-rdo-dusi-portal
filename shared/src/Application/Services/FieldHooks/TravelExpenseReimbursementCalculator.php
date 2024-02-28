<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

class TravelExpenseReimbursementCalculator
{
    private const YEARLY_INCOME_LIMIT_LOW = 34999;
    private const YEARLY_INCOME_LIMIT_MIDDLE = 50001;
    private const YEARLY_INCOME_LIMIT_HIGH = 65000;

    private const TRAVEL_REIMBURSEMENT_PER_KILOMETER_LOW_INCOME_PRIMARY_EDUCATION = 0.12;
    private const TRAVEL_REIMBURSEMENT_PER_KILOMETER_MIDDLE_INCOME_PRIMARY_EDUCATION = 0.10;
    private const TRAVEL_REIMBURSEMENT_PER_KILOMETER_HIGH_INCOME_PRIMARY_EDUCATION = 0.09;

    private const TRAVEL_REIMBURSEMENT_PER_KILOMETER_LOW_INCOME_SECONDARY_EDUCATION = 0.13;
    private const TRAVEL_REIMBURSEMENT_PER_KILOMETER_MIDDLE_INCOME_SECONDARY_EDUCATION = 0.11;
    private const TRAVEL_REIMBURSEMENT_PER_KILOMETER_HIGH_INCOME_SECONDARY_EDUCATION = 0.10;


    public static function calculateForPrimaryEducation(int $totalIncome): float
    {
        if ($totalIncome <= self::YEARLY_INCOME_LIMIT_LOW) {
            return self::TRAVEL_REIMBURSEMENT_PER_KILOMETER_LOW_INCOME_PRIMARY_EDUCATION;
        }

        if ($totalIncome <= self::YEARLY_INCOME_LIMIT_MIDDLE) {
            return self::TRAVEL_REIMBURSEMENT_PER_KILOMETER_MIDDLE_INCOME_PRIMARY_EDUCATION;
        }

        if ($totalIncome <= self::YEARLY_INCOME_LIMIT_HIGH) {
            return self::TRAVEL_REIMBURSEMENT_PER_KILOMETER_HIGH_INCOME_PRIMARY_EDUCATION;
        }

        return 0;
    }

    public static function calculateForSecondaryEducation(int $totalIncome): float
    {
        if ($totalIncome <= self::YEARLY_INCOME_LIMIT_LOW) {
            return self::TRAVEL_REIMBURSEMENT_PER_KILOMETER_LOW_INCOME_SECONDARY_EDUCATION;
        }

        if ($totalIncome <= self::YEARLY_INCOME_LIMIT_MIDDLE) {
            return self::TRAVEL_REIMBURSEMENT_PER_KILOMETER_MIDDLE_INCOME_SECONDARY_EDUCATION;
        }

        if ($totalIncome <= self::YEARLY_INCOME_LIMIT_HIGH) {
            return self::TRAVEL_REIMBURSEMENT_PER_KILOMETER_HIGH_INCOME_SECONDARY_EDUCATION;
        }

        return 0;
    }
}
