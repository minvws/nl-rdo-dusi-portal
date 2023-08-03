<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ApplicationService;
use App\Shared\Models\Application\FileUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Illuminate\Support\Facades\Log;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly FileUpload $fileUpload)
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(ApplicationService $applicationService): void
    {
        try{
            $applicationService->processFileUpload($this->fileUpload);
            dd("processed");
        } catch(Throwable $e) {
            Log::error($e);
//            dd($e);
        }
    }
}
