<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Actions;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\StatefulGuard;
use MinVWS\DUSi\Assessment\API\Events\Logging\PasswordResetEvent;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;

class CompletePasswordReset
{
    public function __construct(
        private readonly LogService $logger
    ) {
    }

    /**
     * Complete the password reset process for the given user.
     *
     * @param StatefulGuard $guard
     * @param  User  $user
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(StatefulGuard $guard, User $user)
    {
        $this->logger->log((new PasswordResetEvent())
            ->withActor($user)
            ->withData([
                'userId' => $user->getAuthIdentifier(),
            ]));

        event(new PasswordReset($user));
    }
}
