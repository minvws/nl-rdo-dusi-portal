<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

readonly class ComparisonCondition extends Condition
{
    public function __construct(
        public int $stage,
        public string $fieldCode,
        public Operator $operator,
        public mixed $value
    ) {
    }

    /**
     * @inheritDoc
     */
    public function evaluate(array $data): bool
    {
        $fieldValue = $data[$this->stage]?->{$this->fieldCode} ?? null;

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
