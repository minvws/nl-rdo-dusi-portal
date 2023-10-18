<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

interface SuccessMessageResultRule
{
    /**
     * @returns array<string>
     */
    public function getSuccessMessages(): array;
}
