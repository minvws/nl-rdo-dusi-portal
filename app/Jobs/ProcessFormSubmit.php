<?php

namespace App\Jobs;

use App\Services\EncryptionService;
use App\Services\ApplicationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFormSubmit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $id, public string $data)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(EncryptionService $encryptionService, ApplicationService $formService): void
    {
        $data = $encryptionService->decryptFormSubmit($this->data);
        $formService->processFormSubmit($this->id, $data);
    }
}
