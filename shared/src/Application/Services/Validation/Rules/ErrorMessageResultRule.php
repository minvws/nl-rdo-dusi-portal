<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

interface ErrorMessageResultRule
{
    /**
     * @returns array<string>
     */
    public function getErrorMessages(): array;
}
