<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Services\Validation\Rules;

use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;

readonly class MockCondition extends Condition
{
    public function __construct(
        public bool $fieldIsRequired = false
    ) {
    }

    /**
     * @inheritDoc
     */
    public function evaluate(array $data): bool
    {
        return $this->fieldIsRequired;
    }
}
