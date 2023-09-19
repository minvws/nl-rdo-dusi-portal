<?php

namespace MinVWS\DUSi\Shared\Subsidy\Models\DTO;

use Illuminate\Contracts\Encryption\Encrypter;
use MinVWS\Codable\Reflection\Attributes\CodableArray;
use MinVWS\DUSi\Shared\Application\Models\Answer;

readonly class AndCondition extends Condition
{
    public function __construct(
        #[CodableArray(Condition::class)] public array $conditions
    ) {
    }

    /**
     * @param array<Answer> $answers
     */
    public function evaluate(array $answers, Encrypter $encrypter): bool
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->evaluate($answers, $encrypter)) {
                return false;
            }
        }

        return true;
    }
}