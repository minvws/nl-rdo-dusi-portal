<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\View\Data;

use MinVWS\DUSi\Shared\User\Models\User;

class UserCredentialsData
{
    public const SESSION_KEY = 'user_credentials_data';

    public function __construct(
        public User $user,
        public string $password,
        public bool $twoFactorAuthenticationReset = false,
    ) {
    }
}
