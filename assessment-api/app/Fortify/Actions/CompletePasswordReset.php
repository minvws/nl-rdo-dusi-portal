<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Actions;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\StatefulGuard;

class CompletePasswordReset
{
    /**
     * Complete the password reset process for the given user.
     *
     * @param StatefulGuard $guard
     * @param  mixed  $user
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(StatefulGuard $guard, $user)
    {
        event(new PasswordReset($user));
    }
}
