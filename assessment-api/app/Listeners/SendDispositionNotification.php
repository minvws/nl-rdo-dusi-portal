<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Events\LetterGeneratedEvent;
use MinVWS\DUSi\Assessment\API\Jobs\SendDispositionNotificationJob;

class SendDispositionNotification implements ShouldQueue
{
    public function handle(LetterGeneratedEvent $event): void
    {
        Log::debug('Dispatch email');

        SendDispositionNotificationJob::dispatch($event->dispositionData);
    }
}
