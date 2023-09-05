<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

class RPCMethods
{
    public const LIST_MESSAGES = 'listMessages';
    public const LIST_APPLICATIONS = 'listApplications';
    public const GET_ACTIONABLE_COUNTS = 'getActionableCounts';
    public const GET_MESSAGE = 'getMessage';
    public const GET_MESSAGE_DOWNLOAD = 'getMessageDownload';
}
