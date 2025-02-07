<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

use MinVWS\Codable\Reflection\Attributes\CodableArray;

readonly class OrCondition extends Condition
{
    public function __construct(
        #[CodableArray(Condition::class)] public array $conditions
    ) {
    }

    /**
     * @inheritDoc
     */
    public function evaluate(array $data): bool
    {
        if (count($this->conditions) === 0) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if ($condition->evaluate($data)) {
                return true;
            }
        }

        return false;
    }
}
