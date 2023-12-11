<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class ViewApplicationEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2012';
    public const EVENT_KEY = 'view_assignment';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_READ;
    }
}
