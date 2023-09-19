<?php

namespace MinVWS\DUSi\Shared\Subsidy\Models\DTO;

use Illuminate\Contracts\Encryption\Encrypter;
use MinVWS\DUSi\Shared\Application\Models\Answer;

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

    /**
     * @param array<Answer> $answers
     */
    public function evaluate(array $answers, Encrypter $encrypter): bool
    {
        $value = $this->getFieldValue($this->fieldCode, $answers, $encrypter);
        return in_array($value, $this->values);
    }
}