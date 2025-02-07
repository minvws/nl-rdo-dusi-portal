<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum MessageDownloadFormat: string
{
    case PDF = 'pdf';
    case HTML = 'html';
}
