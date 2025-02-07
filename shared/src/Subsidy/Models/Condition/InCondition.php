<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

readonly class InCondition extends Condition
{
    public function __construct(
        public int $stage,
        public string $fieldCode,
        public array $values
    ) {
    }

    /**
     * @inheritDoc
     */
    public function evaluate(array $data): bool
    {
        $fieldValue = $data[$this->stage]?->{$this->fieldCode} ?? null;
        return in_array($fieldValue, $this->values);
    }
}
