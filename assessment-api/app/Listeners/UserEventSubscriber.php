<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use MinVWS\DUSi\Assessment\API\Events\Logging\LoginEvent;
use MinVWS\DUSi\Assessment\API\Events\Logging\LogoutEvent;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;

readonly class UserEventSubscriber
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private LogService $logger,
    ) {
    }

    public function subscribe(): array
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout'
        ];
    }

    public function handleUserLogin(Login $event): void
    {
        $user = $event->user;
        assert($user instanceof User);

        $this->logger->log((new LoginEvent())
            ->withActor($user)
            ->withData([
                'userId' => $user->getAuthIdentifier(),
            ]));
    }

    public function handleUserLogout(Logout $event): void
    {
        $user = $event->user;
        assert($user instanceof User);

        $this->logger->log((new LogoutEvent())
            ->withActor($user)
            ->withData([
                'userId' => $event->user->getAuthIdentifier(),
            ]));
    }
}
