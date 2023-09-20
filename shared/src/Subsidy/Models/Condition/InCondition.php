<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

readonly class InCondition extends Condition
{
    /**
     * @param string $fieldCode
     * @param array<string|bool|int|float|null> $values
     */
    public function __construct(
        public string $fieldCode,
        public array $values
    ) {
    }

    public function evaluate(object $data): bool
    {
        $fieldValue = $data->{$this->fieldCode} ?? null;
        return in_array($fieldValue, $this->values);
    }
}
