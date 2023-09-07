<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Listeners;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Jobs\GenerateLetterJob;
use MinVWS\DUSi\Shared\Application\Events\ApplicationStageDecidedEvent;

class GenerateLetter
{
    public function handle(ApplicationStageDecidedEvent $event): void
    {
        Log::debug('Dispatch letter generation job');

        GenerateLetterJob::dispatch($event->applicationStage);
    }
}
