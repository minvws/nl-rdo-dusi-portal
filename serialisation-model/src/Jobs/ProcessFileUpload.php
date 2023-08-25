<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FileUploadHandlerInterface;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use Throwable;

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
    public function handle(FileUploadHandlerInterface $uploadHandler): void
    {
        $uploadHandler->handle($this->fileUpload);
    }
}
