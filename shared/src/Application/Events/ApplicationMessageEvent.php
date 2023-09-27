<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;

class ApplicationMessageEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly SubsidyStageTransitionMessage $message,
        public readonly ApplicationStage $applicationStage
    ) {
    }
}
