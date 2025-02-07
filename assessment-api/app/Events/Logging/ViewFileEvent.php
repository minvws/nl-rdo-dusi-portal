<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class ViewFileEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2015';
    public const EVENT_KEY = 'view_file';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
