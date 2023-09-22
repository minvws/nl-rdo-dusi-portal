<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;

class RequiredConditionRule implements DataAwareRule, ValidationRule
{
    public function __construct(
        private readonly int $stage,
        private readonly Condition $condition
    ) {
    }

    private array $data = [];

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = [$this->stage => (object)$this->data];
        if ($this->condition->evaluate($data) && empty($value)) {
            $fail('The attribute ' . $attribute . ' is required.');
        }
    }
}
