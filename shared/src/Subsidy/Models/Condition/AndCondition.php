<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

use MinVWS\Codable\Reflection\Attributes\CodableArray;

readonly class AndCondition extends Condition
{
    public function __construct(
        #[CodableArray(Condition::class)] public array $conditions
    ) {
    }

    public function evaluate(object $data): bool
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->evaluate($data)) {
                return false;
            }
        }

        return true;
    }
}
