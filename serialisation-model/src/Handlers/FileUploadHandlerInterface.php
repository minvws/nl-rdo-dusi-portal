<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Handlers;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;

interface FileUploadHandlerInterface
{
    public function handle(FileUpload $fileUpload): void;
}
