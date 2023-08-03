<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ApplicationService;
use App\Shared\Models\Application\FormSubmit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessFormSubmit implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly FormSubmit $formSubmit)
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(ApplicationService $applicationService): void
    {
        $applicationService->processFormSubmit($this->formSubmit);
    }
}
