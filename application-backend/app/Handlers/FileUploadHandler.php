<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Handlers;

use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FileUploadHandlerInterface;

class FileUploadHandler implements FileUploadHandlerInterface
{
    public function __construct(private ApplicationService $applicationService)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(FileUpload $fileUpload): void
    {
        $this->applicationService->processFileUpload($fileUpload);
    }
}
