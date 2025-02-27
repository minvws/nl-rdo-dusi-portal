<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

class RPCMethods
{
    public const LIST_MESSAGES = 'listMessages';
    public const GET_MESSAGE = 'getMessage';
    public const GET_MESSAGE_DOWNLOAD = 'getMessageDownload';

    public const CREATE_APPLICATION = 'createApplication';
    public const UPLOAD_APPLICATION_FILE = 'uploadApplicationFile';
    public const SAVE_APPLICATION = 'saveApplication';
    public const VALIDATE_APPLICATION = 'validateApplication';
    public const LIST_APPLICATIONS = 'listApplications';
    public const GET_APPLICATION = 'getApplication';
    public const GET_APPLICATION_FILE = 'getApplicationFile';

    public const GET_ACTIONABLE_COUNTS = 'getActionableCounts';

    public const GET_SUBSIDY_OVERVIEW = 'getSubsidyOverview';
}
