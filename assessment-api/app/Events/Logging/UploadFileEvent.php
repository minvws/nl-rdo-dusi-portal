<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class UploadFileEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2016';
    public const EVENT_KEY = 'upload_file';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_CREATE;
    }
}
