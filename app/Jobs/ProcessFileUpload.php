<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $formId, public string $fileId, public string $data)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        echo "Received ProcessFileUpload job for form \"{$this->formId}\" with file id \"{$this->fileId}\"\n";
    }
}
