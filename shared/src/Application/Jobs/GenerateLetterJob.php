<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;

class GenerateLetterJob implements ShouldQueue, ShouldBeEncrypted
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        #[WithoutRelations] public readonly SubsidyStageTransitionMessage $message,
        #[WithoutRelations] public readonly ApplicationStageTransition $transition
    ) {
    }

    public function handle(LetterService $letterService): void
    {
        $letterService->generateLetters($this->message, $this->transition);
    }
}
