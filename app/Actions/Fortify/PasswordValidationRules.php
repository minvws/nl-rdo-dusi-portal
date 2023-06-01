<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return (\Laravel\Fortify\Rules\Password|string)[]
     *
     * @psalm-return list{'required', 'string', \Laravel\Fortify\Rules\Password, 'confirmed'}
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', new Password, 'confirmed'];
    }
}
