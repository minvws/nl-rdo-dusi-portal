<?php

namespace App\Jobs;

use App\Services\ApplicationService;
use App\Shared\Models\Application\FileUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly FileUpload $fileUpload) {
    }

    /**
     * @throws Throwable
     */
    public function handle(ApplicationService $applicationService): void
    {
        $applicationService->processFileUpload($this->fileUpload);
    }
}
