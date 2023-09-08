<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Assessment\API\Services\LetterService;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

class GenerateLetterJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly ApplicationStage $applicationStage
    ) {
    }

    public function handle(LetterService $letterService): void
    {
        $letterService->generateLetters($this->applicationStage);
    }
}
