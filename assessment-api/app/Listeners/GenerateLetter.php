<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Listeners;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Jobs\GenerateLetterJob;
use MinVWS\DUSi\Shared\Application\Events\ApplicationStageVersionDecidedEvent;

class GenerateLetter
{
    public function handle(ApplicationStageVersionDecidedEvent $event): void
    {
        Log::debug('Dispatch letter generation job');

        GenerateLetterJob::dispatch($event->applicationStageVersion);
    }
}
