<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, Password|Rule|string|array<Rule|string>>
     */
    protected function passwordRules(): array
    {
        $passwordRule = Password::min(12)
            ->mixedCase()
            ->numbers()
            ->symbols();

        return [
            'required',
            'string',
            $passwordRule,
            'confirmed',
        ];
    }
}
