<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use MinVWS\DUSi\Assessment\API\Listeners\GenerateLetter;
use MinVWS\DUSi\Assessment\API\Listeners\SendDispositionNotification;
use MinVWS\DUSi\Assessment\API\Listeners\UserEventSubscriber;
use MinVWS\DUSi\Shared\Application\Events\ApplicationMessageEvent;
use MinVWS\DUSi\Shared\Application\Events\LetterGeneratedEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ApplicationMessageEvent::class => [
            GenerateLetter::class
        ],
        LetterGeneratedEvent::class => [
            SendDispositionNotification::class
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array<class-string>
     */
    protected $subscribe = [
        UserEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
