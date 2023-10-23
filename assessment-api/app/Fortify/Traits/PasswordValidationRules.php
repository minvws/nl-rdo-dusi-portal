<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Traits;

use Illuminate\Contracts\Validation\Rule;
use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, Rule|array|string>
     */
    protected function passwordRules(): array
    {
        $passwordRule = new Password();
        $passwordRule
            ->requireUppercase()
            ->requireNumeric()
            ->requireSpecialCharacter()
            ->length(12);

        return [
            'required',
            'string',
            $passwordRule,
            'confirmed',
        ];
    }
}
