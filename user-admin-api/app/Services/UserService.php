<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Services;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Collection;
use JsonException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use MinVWS\DUSi\User\Admin\API\Models\User;
use MinVWS\DUSi\User\Admin\API\View\Data\UserCredentialsData;
use Minvws\HorseBattery\PasswordGenerator;

class UserService
{
    public function __construct(
        protected PasswordGenerator $passwordGenerator,
        protected Hasher $passwordHasher,
        protected TwoFactorAuthenticationProvider $twoFactorAuthenticationProvider,
        protected Encrypter $encrypter,
    ) {
    }

    public function generatePassword(int $wordCount = 4): string
    {
        return $this->passwordGenerator->generate($wordCount);
    }

    public function createUser(
        string $name = '',
        string $email = '',
        string $password = '',
        string $organisationId = '',
    ): User {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $this->passwordHasher->make($password),
            'organisation_id' => $organisationId,
        ]);
        $user->forceFill($this->getNewTwoFactorSecrets());
        $user->save();

        return $user;
    }

    public function resetUserPassword(
        User $user,
        bool $resetPassword = false,
        bool $resetTwoFactor = false,
    ): UserCredentialsData {
        $update = [];
        if ($resetPassword) {
            $password = $this->passwordGenerator->generate(4);
            $update['password'] = $this->passwordHasher->make($password);
        }

        if ($resetTwoFactor) {
            $update = array_merge($update, $this->getNewTwoFactorSecrets());
        }

        if (!empty($update)) {
            $user->forceFill($update);
            $user->save();
        }

        return new UserCredentialsData(
            $user,
            $password ?? '',
            $resetTwoFactor,
        );
    }

    /**
     * @return array<string, string>
     */
    protected function getNewTwoFactorSecrets(): array
    {
        try {
            return [
                'two_factor_secret' => $this->encrypter
                    ->encrypt($this->twoFactorAuthenticationProvider->generateSecretKey()),
                'two_factor_recovery_codes' => $this->encrypter->encrypt(json_encode(Collection::times(8, function () {
                    return $this->passwordGenerator->generate(4, '-');
                })->all(), JSON_THROW_ON_ERROR)),
            ];
        } catch (JsonException) {
            return [];
        }
    }
}
