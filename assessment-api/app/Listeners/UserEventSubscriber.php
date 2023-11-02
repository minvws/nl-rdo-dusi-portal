<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use MinVWS\DUSi\Assessment\API\Events\Logging\LoginEvent;
use MinVWS\DUSi\Assessment\API\Events\Logging\LogoutEvent;
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
        $this->logger->log((new LoginEvent())
            ->withData([
                'userId' => $event->user->getAuthIdentifier(),
            ]));
    }

    public function handleUserLogout(Logout $event): void
    {
        $this->logger->log((new LogoutEvent())
           ->withData([
              'userId' => $event->user->getAuthIdentifier(),
          ]));
    }
}
