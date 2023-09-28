<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Listeners;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\Events\ApplicationMessageEvent;
use MinVWS\DUSi\Shared\Application\Jobs\GenerateLetterJob;

class GenerateLetter
{
    public function handle(ApplicationMessageEvent $event): void
    {
        Log::debug('Dispatch letter generation job');

        GenerateLetterJob::dispatch($event->message, $event->applicationStage);
    }
}
