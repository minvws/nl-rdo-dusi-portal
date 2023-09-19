<?php

namespace MinVWS\DUSi\Shared\Subsidy\Models\DTO;

use Illuminate\Contracts\Encryption\Encrypter;
use MinVWS\DUSi\Shared\Application\Models\Answer;

readonly class ComparisonCondition extends Condition
{
    public function __construct(
        public string $fieldCode,
        public Operator $operator,
        public mixed $value
    ) {
    }

    /**
     * @param array<Answer> $answers
     */
    public function evaluate(array $answers, Encrypter $encrypter): bool
    {
        $fieldValue = $this->getFieldValue($this->fieldCode, $answers, $encrypter);

        return match ($this->operator) {
            Operator::Equal => $fieldValue == $this->value,
            Operator::Identical => $fieldValue === $this->value,
            Operator::NotEqual => $fieldValue != $this->value,
            Operator::NotIdentical => $fieldValue !== $this->value,
            Operator::GreaterThan => $fieldValue > $this->value,
            Operator::GreaterThanOrEqualTo => $fieldValue >= $this->value,
            Operator::LessThan => $fieldValue < $this->value,
            Operator::LessThanOrEqualTo => $fieldValue <= $this->value
        };
    }
}