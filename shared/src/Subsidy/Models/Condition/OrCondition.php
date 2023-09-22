<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

use Illuminate\Contracts\Encryption\Encrypter;
use MinVWS\Codable\Reflection\Attributes\CodableArray;
use MinVWS\DUSi\Shared\Application\Models\Answer;

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
