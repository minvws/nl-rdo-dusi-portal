<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\Disk;

class LetterRepository
{
    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = Storage::disk(Disk::APPLICATION_FILES);
    }

    public function getHtmlContent(ApplicationMessage $message): string
    {
        return $this->filesystem->get($message->html_path) ?: '';
    }

    public function getPdfContent(ApplicationMessage $message): string
    {
        return $this->filesystem->get($message->pdf_path) ?: '';
    }
}
