<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use MinVWS\DUSi\Assessment\API\Fortify\Traits\PasswordValidationRules;
use MinVWS\DUSi\Shared\User\Models\User;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        $passwordIsExpired = $user->passwordExpired();

        Validator::make($input, [
            'current_password' => [
                Rule::requiredIf(fn() => !$passwordIsExpired),
                'string',
                'current_password:web'
            ],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $passwordHash = Hash::make($input['password']);

        $user->forceFill([
            'password' => $passwordHash,
            'password_updated_at' => now(),
        ])->save();

        if ($passwordIsExpired) {
            session()->put([
                'password_hash_web' => $passwordHash,
            ]);
        }
    }
}
