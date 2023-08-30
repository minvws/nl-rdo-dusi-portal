<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum ApplicationStatus: string
{
    case New = 'new';
    case Approved = 'approved';
    case Denied = 'denied';
    case RequestForChanges = 'requestForChanges';
}
