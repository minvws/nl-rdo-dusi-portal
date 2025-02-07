<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;

class ApplicationMessageEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        #[WithoutRelations] public readonly SubsidyStageTransitionMessage $message,
        #[WithoutRelations] public readonly ApplicationStageTransition $transition
    ) {
    }
}
