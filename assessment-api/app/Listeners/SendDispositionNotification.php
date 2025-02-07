<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Listeners;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\Events\LetterGeneratedEvent;
use MinVWS\DUSi\Shared\Application\Jobs\SendDispositionNotificationJob;

class SendDispositionNotification
{
    public function handle(LetterGeneratedEvent $event): void
    {
        Log::debug('Dispatch email');

        SendDispositionNotificationJob::dispatch($event->dispositionData);
    }
}
