<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Application $application
    ) {
    }
}
