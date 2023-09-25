<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

readonly class IsEmptyCondition extends Condition
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
        return empty($fieldValue);
    }
}
