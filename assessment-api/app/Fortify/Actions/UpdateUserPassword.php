<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Laravel\Fortify\Rules\Password;
use MinVWS\DUSi\Shared\User\Models\User;

class UpdateUserPassword implements UpdatesUserPasswords
{
    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => [
                Rule::requiredIf(fn() => !$user->passwordExpired()),
                'string',
                'current_password:web'
            ],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
            'password_updated_at' => now(),
        ])->save();
    }

    private function passwordRules(): array
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
